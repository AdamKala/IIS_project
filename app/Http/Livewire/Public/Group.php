<?php

namespace App\Http\Livewire\Public;

use App\Models\Comment;
use App\Models\GroupUser;
use App\Models\SubComment;
use App\Models\Tags;
use App\Models\TagsThread;
use App\Models\Thread;
use App\Models\User;
use App\Models\UserHasRoles;
use App\Models\Vote;
use App\Traits\AlertHelper;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Livewire\WithFileUploads;

class Group extends Component
{
    use AlertHelper;

    protected $listeners = [
        'update-threads' => '$refresh',
        'destroyConfirmed' => 'removeThread',
        'destroyGroupConfirmed' => 'removeGroup',
        'removeConfirmed' => 'removeUserFromGroup'
    ];

    public Collection $threads;

    public $slug;

    public $thread;
    public $search;
    public $title;
    public $editingTitle;
    public $editingId;
    public $editingSlug;
    public $editingText;
    public $inputField;
    public $editingTag;

    public function mount()
    {
        $this->inputField = '';
        $group = \App\Models\Group::where('slug', $this->slug)->first();
        $this->threads = Thread::where('group_id', $group->id)->get();
        $this->editingId = -1;
        $this->editingTitle = '';
        $this->editingSlug = '';
        $this->editingTag = '';
        $this->search = '';
        $this->emit('update-threads');
    }

    public function search()
    {
        if (empty($this->search)) {
            $group = \App\Models\Group::where('slug', $this->slug)->first();
            $this->threads = Thread::where('group_id', $group->id)->get();
            return;
        }

        $group = \App\Models\Group::where('slug', $this->slug)->first();
        $tags = Tags::where('name', 'LIKE', '%' . $this->search . '%')->get();
        $tagIds = $tags->pluck('id');

        $this->threads = Thread::where('group_id', $group->id)
            ->where(function ($query) use ($tagIds) {
                $query->where('name', 'LIKE', '%' . $this->search . '%')
                    ->orWhereHas('tags', function ($query) use ($tagIds) {
                        $query->whereIn('id', $tagIds)
                            ->where('name', 'LIKE', '%' . $this->search . '%');
                    });
            })
            ->get();

        $this->emit('update-threads');
    }

    public function saveEdit()
    {
        if ($this->editingId != -1) {
            $model = Thread::find($this->editingId);
            if ($model) {
                if (mb_strlen($this->editingInput) > 0) {
                    if ($model['slug'] != $this->editingSlug) {
                        $groupModel = Thread::query()->where('slug', '=', $this->editingSlug)->first();
                        if ($groupModel) {
                            $this->showAlert('error', 'Slug se již používá!');
                            return;
                        }
                    }
                    $model['name'] = $this->editingInput;
                    $model['slug'] = Str::slug($this->editingSlug);
                    $model['text'] = $this->editingText;
                    $model->save();

                    $threadId = $this->editingId;

                    $existingTags = Tags::where('thread_id', $threadId)->pluck('name')->toArray();

                    $newTags = explode(' ', $this->editingTag);

                    TagsThread::where('thread_id', $threadId)->delete();

                    foreach ($newTags as $tagName) {
                        $tag = Tags::firstOrCreate(['name' => $tagName, 'thread_id' => $threadId,]);
                        TagsThread::create([
                            'tag_id' => $tag->id,
                            'thread_id' => $threadId,
                        ]);
                    }

                    $this->editingId = -1;
                    $this->editingInput = '';
                    $this->editingSlug = '';
                    $this->editingText = '';
                    $this->emit('update-threads');
                    $this->showAlert('success', 'Vlákno úspěšně upraveno.');
                }
            }
        }
    }


    public function toggleEdit($id)
    {
        if ($this->editingId == $id) {
            $this->editingId = -1;
            $this->editingInput = '';
            $this->editingSlug = '';
            $this->editingTag = '';
            $this->editingText = '';
        } else {
            $model = Thread::find($id);
            if ($model) {
                $this->editingId = $id;
                $this->editingInput = $model['name'];
                $this->editingSlug = $model['slug'];
                $this->editingText = $model['text'];
                $existingTags = Tags::where('thread_id', $id)->pluck('name')->toArray();
                $this->editingTag = implode(' ', $existingTags);
            }
        }
        $this->emit('update-threads');
    }

    public function removeThread($response)
    {
        $id = $response['data']['inputAttributes']['value'];
        Thread::destroy($id);
        $this->showAlert('success', 'Vlákno úspěšně odstraněno.');
        $this->emit('update-threads');
    }

    public function deleteThread($id)
    {
        $model = Thread::find($id);
        if ($model) {
            $this->confirm('Opravdu chcete odstranit vlákno ' . $model['name'] . '?', [
                'inputAttributes' => [
                    'value' => $id,
                ],
                'onConfirmed' => 'destroyConfirmed',
                'position' => 'top',
                'cancelButtonText' => 'Zrušit',
                'confirmButtonText' => 'Smazat',
            ]);
        } else {
            $this->showAlert('success', 'Vlákno nebylo nalezeno.');
        }
    }

    public function toggleEnabled($id, $group_id)
    {
        $groupUser = GroupUser::where('user_id', $id)
            ->where('group_id', $group_id)
            ->first();
        if ($groupUser) {
            $groupUser['enabled'] = !$groupUser['enabled'];
            $groupUser->save();
            $this->showAlert('success', 'Status změněn.');
            $this->emit('update-threads');
        }
    }

    public function toggleGroupEnabled($id)
    {
        $group = \App\Models\Group::find($id);
        if ($group) {
            $group['enabled'] = !$group['enabled'];
            $group->save();
            $this->showAlert('success', 'Status změněn.');
            $this->emit('update-threads');
        }
    }

    public function joinGroup($groupid)
    {
        $user = User::find(Auth::id());
        if ($user == null) {
            $this->showAlert('error', 'Nejste zaregistrovaný!');
        } else {
            $request = new \App\Models\GroupJoinRequest;
            $request['group_id'] = $groupid;
            $request['user_id'] = $user->id;
            $request->save();

            $this->showAlert('success', 'Žádost odeslána!');
        }
    }

    public function createThread()
    {
        if (mb_strlen($this->inputField) > 0) {
            $name = $this->inputField;
            $group = Thread::where('name', '=', $name)->first();
            if ($group) {
                $this->showAlert('error', 'Vlákno již existuje!');
                return;
            }
            $slug = Str::slug($name);
            $i = 0;
            while (Thread::query()->where('slug', '=', $slug)->first()) {
                $slug = Str::slug($name . ' ' . $i);
                $i++;
            }

            $thread = new Thread;
            $thread['name'] = $name;
            $thread['slug'] = $slug;
            $thread['privacy'] = true;
            $thread['text'] = '';
            $thread->save();
            $this->showAlert('success', 'Vlákno úspěšně vytvořeno.');
            $this->inputField = '';
            $this->thread = Thread::all();
            $this->emit('update-threads');
        }
    }

    public function manageGroupRequest($groupid)
    {
        $user = \App\Models\User::find(Auth::id());
        $isInGroup = GroupUser::where('group_id', $groupid)->where('user_id', Auth::id())->exists();
        if ($user == null) {
            $this->showAlert('error', 'Nejste zaregistrovaný!');
        } else if (!$isInGroup) {
            $this->showAlert('error', 'Nejste ve skupině!');
        } else {
            $request = new \App\Models\GroupManageRequest;
            $request['group_id'] = $groupid;
            $request['user_id'] = $user->id;
            $request->save();

            $this->showAlert('success', 'Žádost odeslána!');
        }
    }

    public function removeUser($response)
    {
        $id = $response['data']['inputAttributes']['value'];
        $users = \App\Models\GroupUser::where('user_id', $id)->get();
        foreach ($users as $user) {
            $user->delete();
        }
        $usersManage = \App\Models\UserManageGroup::where('user_id', $id)->get();
        foreach ($usersManage as $uM) {
            $uM->delete();
        }

        $usersManageRequest = \App\Models\GroupManageRequest::where('user_id', $id)->get();
        foreach ($usersManageRequest as $uMR) {
            $uMR->delete();
        }

        $usersJoinRequest = \App\Models\GroupJoinRequest::where('user_id', $id)->get();
        foreach ($usersJoinRequest as $uJR) {
            $uJR->delete();
        }

        $votes = \App\Models\Vote::where('created_by', $id)->get();
        foreach ($votes as $vote) {
            $vote->delete();
        }

        $threads = \App\Models\Thread::where('created_by', $id)->get();
        foreach ($threads as $thread) {
            $thread->delete();
        }

        $comments = \App\Models\Comment::where('created_by', $id)->get();
        foreach ($comments as $comm) {
            $comm->delete();
        }

        $subcomments = \App\Models\SubComment::where('created_by', $id)->get();
        foreach ($subcomments as $subcomm) {
            $subcomm->delete();
        }

        $models = \App\Models\UserHasRoles::where('model_id', $id)->get();
        foreach ($models as $mod) {
            $mod->delete();
        }

        $groups = \App\Models\Group::where('created_by', $id)->get();
        foreach ($groups as $group) {
            $group->delete();
        }

        \App\Models\User::destroy($id);
        $this->showAlert('success', 'Účet smazán.');
        $this->emit('update-users');
        return redirect('/');
    }

    public function deleteUser($id)
    {
        $user = \App\Models\User::find($id);
        $table = UserHasRoles::query()->where('model_id', $id)->where('role_id', 1)->exists();
        if ($table) {
            $this->showAlert('error', 'Účet s Admin rolí nelze smazat.');
        }
        if ($user && !$table) {
            $this->confirm('Opravdu chcete smazat svůj účet?', [
                'inputAttributes' => [
                    'value' => $id,
                ],
                'onConfirmed' => 'removeUser',
                'position' => 'top',
                'cancelButtonText' => 'Zrušit',
                'confirmButtonText' => 'Smazat',
            ]);
        }
    }

    public function removeGroup($response)
    {
        $id = $response['data']['inputAttributes']['value'];
        $threads = \App\Models\Thread::where('group_id', $id)->get();
        $usermanagegroup = \App\Models\UserManageGroup::where('group_id', $id)->get();
        $joinRequest = \App\Models\GroupJoinRequest::where('group_id', $id)->get();
        $manageRequest = \App\Models\GroupManageRequest::where('group_id', $id)->get();
        $groupUser = \App\Models\GroupUser::where('group_id', $id)->get();
        $userHasRole = UserHasRoles::where('group_id', $id)->get();
        if ($groupUser != null) {
            foreach ($groupUser as $gU) {
                $gU->delete();
            }
        }
        if ($manageRequest != null) {
            foreach ($manageRequest as $mR) {
                $mR->delete();
            }
        }
        if ($joinRequest != null) {
            foreach ($joinRequest as $jR) {
                $jR->delete();
            }
        }
        if ($threads != null) {
            foreach ($threads as $thread) {
                $thread->delete();

                $subComm = SubComment::where('thread_id', $thread->id)->get();
                foreach ($subComm as $sCm) {
                    $sCm->delete();
                }

                $comm = Comment::where('thread_id', $thread->id)->get();
                foreach ($comm as $cm) {
                    $cm->delete();
                }
            }
        }
        if ($usermanagegroup != null) {
            foreach ($usermanagegroup as $umg) {
                $umg->delete();
            }
        }

        if ($userHasRole != null) {
            foreach ($userHasRole as $uHR) {
                $uHR->delete();
            }
        }
        \App\Models\Group::destroy($id);
        $this->showAlert('success', 'Skupina úspěšně odstraněna.');
        $this->emit('update-groups');
        redirect('/');
    }

    public function deleteGroup($id)
    {
        $model = \App\Models\Group::find($id);
        if ($model) {
            $this->confirm('Opravdu chcete odstranit skupinu ' . $model['name'] . '?', [
                'inputAttributes' => [
                    'value' => $id,
                ],
                'onConfirmed' => 'destroyGroupConfirmed',
                'position' => 'top',
                'cancelButtonText' => 'Zrušit',
                'confirmButtonText' => 'Smazat',
            ]);
        } else {
            $this->showAlert('success', 'Skupina nebyla nalezena.');
        }
    }

    public function removeUserFromGroup($response)
    {
        $userId = $response['data']['inputAttributes']['userId'];
        $groupId = $response['data']['inputAttributes']['value'];
        $users = \App\Models\GroupUser::where('user_id', $userId)->where('group_id', $groupId)->get();
        foreach ($users as $user) {
            $user->delete();
        }

        $groupJoinRequests = \App\Models\GroupJoinRequest::where('user_id', $userId)->where('group_id', $groupId)->get();
        foreach ($groupJoinRequests as $gJR) {
            $gJR->delete();
        }

        $groupManageRequests = \App\Models\GroupManageRequest::where('user_id', $userId)->where('group_id', $groupId)->get();
        foreach ($groupManageRequests as $gMR) {
            $gMR->delete();
        }

        $userHasRole = UserHasRoles::where('model_id', $userId)->where('group_id', $groupId)->get();
        foreach ($userHasRole as $uHR) {
            $uHR->delete();
        }

        $userMod = \App\Models\UserManageGroup::where('group_id', $groupId)->where('role_type', 'moderator')->first();
        if ($userMod) {
            $userMod->delete();
        }

        $userManage = \App\Models\UserManageGroup::where('group_id', $groupId)->where('user_id', $userId)->where('role_type', 'spravce')->first();
        if ($userManage) {
            $userAdd = \App\Models\UserManageGroup::where('group_id', $groupId)->first();
            $userAdd['role_type'] = 'spravce';
            $userAdd->save();

            $userHasRole0 = UserHasRoles::where('group_id', $groupId)->where('role_id', 5)->first();
            $userHasRole0['role_id'] = 4;
            $userHasRole0->save();

            $userManage->delete();
        }
    }

    public function leaveGroup($userId, $groupId)
    {
        $userInGroup = UserHasRoles::where('group_id', $groupId)
            ->where('model_id', $userId)
            ->whereNotIn('role_id', [4, 5])
            ->first();

        $allUsers = GroupUser::where('group_id', $groupId)->where('user_id', $userId)->first();

        $usersCount = \App\Models\GroupUser::where('group_id', $groupId)->count();
        $userManage = \App\Models\UserManageGroup::where('group_id', $groupId)
            ->where('role_type', 'moderator')->exists();
        $userSpravce = \App\Models\UserManageGroup::where('group_id', $groupId)
            ->where('role_type', 'spravce')->where('user_id', $userId)->exists();

        if ($usersCount == 1 || (!$userManage && $userSpravce)) {
            $this->confirm('Smazat skupinu?', [
                'inputAttributes' => [
                    'userId' => $userId,
                    'value' => $groupId,
                ],
                'onConfirmed' => 'destroyGroupConfirmed',
                'position' => 'top',
                'cancelButtonText' => 'Ne',
                'confirmButtonText' => 'Ano',
            ]);
        } elseif ($userInGroup) {
            $this->confirm('Jste si jistí?', [
                'inputAttributes' => [
                    'userId' => $userId,
                    'value' => $groupId,
                ],
                'onConfirmed' => 'removeConfirmed',
                'position' => 'top',
                'cancelButtonText' => 'Ne',
                'confirmButtonText' => 'Ano',
            ]);
        } elseif ($allUsers) {
            $this->confirm('Jste si jistí?', [
                'inputAttributes' => [
                    'userId' => $userId,
                    'value' => $groupId,
                ],
                'onConfirmed' => 'removeConfirmed',
                'position' => 'top',
                'cancelButtonText' => 'Ne',
                'confirmButtonText' => 'Ano',
            ]);
        } else {
            $this->showAlert('error', 'Účet není ve skupině.');
        }
    }

    public function render()
    {
        return view('livewire.public.group');
    }
}
