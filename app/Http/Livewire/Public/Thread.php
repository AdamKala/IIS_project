<?php

namespace App\Http\Livewire\Public;

use App\Models\Comment;
use App\Models\Group;
use App\Models\SubComment;
use App\Models\User;
use App\Models\Vote;
use App\Traits\AlertHelper;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Livewire\WithFileUploads;

class Thread extends Component
{
    use AlertHelper, WithFileUploads;

    protected $listeners = [
        'update-threads' => '$refresh',
        'destroyConfirmed' => 'removeComment',
        'destroySubConfirmed' => 'removeSubcomment'
    ];

    public Collection $threads;


    public $slug = '';
    public $thread = '';
    public $commentAdd;
    public $subcommentAdd;

    public $addingSubComment = false;
    public $editingText;
    public $editingSubText;
    public $editingId;
    public $editingSubId;

    public $sortByRating;
    public $sortByName;
    public $sortByDate;

    public $threadid;

    public $commentid;

    public function mount()
    {
        $this->addingSubComment = false;
        $this->commentAdd = '';
        $this->subcommentAdd = '';
        $this->editingId = -1;
        $this->editingSubId = -1;
        $this->editingSubText = '';
        $this->editingText = '';
        $this->emit('update-threads');
    }

    public function sortBy($number)
    {
        if ($number == 1) {
            if ($this->sortByName == 1) {
                $this->sortByName = 0;
            } else {
                $this->sortByName = 1;
                $this->sortByRating = 0;
                $this->sortByDate = 0;
            }
        } else if ($number == 2) {
            if ($this->sortByRating == 1) {
                $this->sortByRating = 0;
            } else {
                $this->sortByName = 0;
                $this->sortByRating = 1;
                $this->sortByDate = 0;
            }
        } else if ($number == 3) {
            if ($this->sortByDate == 1) {
                $this->sortByDate = 0;
            } else {
                $this->sortByName = 0;
                $this->sortByRating = 0;
                $this->sortByDate = 1;
            }
        }
    }

    public function upvote($userId, $commentId)
    {
        $existingUpvote = Vote::where('user_id', $userId)
            ->where('comment_id', $commentId)
            ->where('vote_type', 'upvote')
            ->first();

        $existingDownvote = Vote::where('user_id', $userId)
            ->where('comment_id', $commentId)
            ->where('vote_type', 'downvote')
            ->first();

        $userDownvote = Vote::where('user_id', $userId)
            ->where('comment_id', $commentId)
            ->where('vote_type', 'downvote')
            ->exists();

        if ($existingUpvote) {
            $comment = \App\Models\Comment::find($commentId);
            $vote = Vote::query()->where('user_id', $userId)->where('comment_id', $commentId)->first();
            if ($vote && $vote['vote_type'] === 'upvote') {
                $comment->rating -= 1;
                Vote::destroy($vote->id);
                $comment->save();
            }

            $this->showAlert('success', 'Hlas odstraněn!');
        } else {
            $comment = \App\Models\Comment::find($commentId);

            if ($comment) {
                $comment->rating += 1;
                if ($userDownvote != null) {
                    $comment->rating += 1;
                }
                $comment->save();

                if ($existingDownvote) {
                    $existingDownvote->delete();
                }

                Vote::create([
                    'user_id' => $userId,
                    'comment_id' => $commentId,
                    'vote_type' => 'upvote',
                ]);
                $this->showAlert('success', 'Hodnocení přidáno!');
            }
        }
    }

    public function downvote($userId, $commentId)
    {
        $existingUpvote = Vote::where('user_id', $userId)
            ->where('comment_id', $commentId)
            ->where('vote_type', 'upvote')
            ->first();

        $existingDownvote = Vote::where('user_id', $userId)
            ->where('comment_id', $commentId)
            ->where('vote_type', 'downvote')
            ->first();

        $userUpvote = Vote::where('user_id', $userId)
            ->where('comment_id', $commentId)
            ->where('vote_type', 'upvote')
            ->exists();

        if ($existingDownvote) {
            $comment = \App\Models\Comment::find($commentId);
            $vote = Vote::query()->where('user_id', $userId)->where('comment_id', $commentId)->first();
            if ($vote && $vote['vote_type'] === 'downvote') {
                $comment->rating += 1;
                Vote::destroy($vote->id);
                $comment->save();
            }
            $this->showAlert('success', 'Hlas odstraněn!');
        } else {
            $comment = \App\Models\Comment::find($commentId);

            if ($comment) {
                $comment->rating -= 1;
                if ($userUpvote != null) {
                    $comment->rating -= 1;
                }
                $comment->save();

                if ($existingUpvote) {
                    $existingUpvote->delete();
                }

                Vote::create([
                    'user_id' => $userId,
                    'comment_id' => $commentId,
                    'vote_type' => 'downvote',
                ]);

                $this->showAlert('success', 'Hodnocení přidáno!');
            }
        }
    }

    public function save()
    {
        $this->validate([
            'image' => 'image'
        ]);
        $this->fileUploading = true;
        $this->emit('$refresh');
    }

    public function saveEdit()
    {
        if ($this->editingId != -1) {
            $model = \App\Models\Comment::find($this->editingId);
            if ($model) {
                if (mb_strlen($this->editingText) > 0) {
                    $model['text'] = $this->editingText;;
                    $model->save();
                    $this->editingId = -1;
                    $this->editingText = '';
                    $this->emit('update-threads');
                    $this->showAlert('success', 'Příspěvek přepsán!');
                }
            }
        }
    }

    public function toggleEdit($id)
    {
        if ($this->editingId == $id) {
            $this->editingId = -1;
            $this->editingText = '';
        } else {
            $model = \App\Models\Comment::find($id);
            if ($model) {
                $this->editingId = $id;
                $this->editingText = $model['text'];
            }
        }
        $this->emit('update-threads');
    }

    public function saveSubEdit()
    {
        if ($this->editingSubId != -1) {
            $model = SubComment::find($this->editingSubId);
            if ($model && mb_strlen($this->editingSubText) > 0) {
                $model['text'] = $this->editingSubText;;
                $model->save();
                $this->editingSubId = -1;
                $this->editingSubText = '';
                $this->emit('update-threads');
                $this->showAlert('success', 'Komentář přepsán!');
            }
        }
    }

    public function toggleSubEdit($id)
    {
        if ($this->editingSubId == $id) {
            $this->editingSubId = -1;
            $this->editingSubText = '';
        } else {
            $model = \App\Models\SubComment::find($id);
            if ($model) {
                $this->editingSubId = $id;
                $this->editingSubText = $model['text'];
            }
        }
        $this->emit('update-threads');
    }

    public function removeComment($response)
    {
        $id = $response['data']['inputAttributes']['value'];

        $subcomments = SubComment::where('comment_id', $id)->get();
        foreach ($subcomments as $subcomment) {
            $subcomment->delete();
        }

        $votes = Vote::where('comment_id', $id)->get();
        foreach ($votes as $vote) {
            $vote->delete();
        }

        \App\Models\Comment::destroy($id);

        $this->showAlert('success', 'Příspěvek úspěšně odstraněn.');
        $this->emit('update-threads');
    }

    public function deleteComment($id)
    {
        $comment = \App\Models\Comment::find($id);
        if ($comment) {
            $this->confirm('Opravdu chcete odstranit příspěvek ' . $comment['name'] . '?', [
                'inputAttributes' => [
                    'value' => $id,
                ],
                'onConfirmed' => 'destroyConfirmed',
                'position' => 'top',
                'cancelButtonText' => 'Zrušit',
                'confirmButtonText' => 'Smazat',
            ]);
        } else {
            $this->showAlert('success', 'Příspěvek nebyl nalezen.');
        }
    }


    public function createComment($thread_id)
    {
        if (mb_strlen($this->commentAdd) > 0) {
            $text = $this->commentAdd;
            $author = Auth::id();

            $commentAuthor = \App\Models\User::where('id', '=', $author)->first();

            $commentAuthorName = $commentAuthor->name;

            $comment = new \App\Models\Comment();
            $comment['author'] = $commentAuthorName;
            $comment['text'] = $text;
            $comment['thread_id'] = $thread_id;
            $comment['created_by'] = Auth::id();
            $comment->save();
            $this->showAlert('success', 'Příspěvek přidán!');
            $this->commentAdd = '';
            $this->emit('update-threads');
        }
    }

    public function removeSubcomment($response)
    {
        $id = $response['data']['inputAttributes']['value'];
        $subcomments = \App\Models\SubComment::where('id', $id)->get();
        \App\Models\SubComment::destroy($id);
        foreach ($subcomments as $subcomment) {
            $subcomment->delete();
        }
        $this->showAlert('success', 'Komentář úspěšně odstraněn.');
        $this->emit('update-threads');
    }

    public function deleteSubcomment($id)
    {
        $subcomment = \App\Models\SubComment::find($id);
        if ($subcomment) {
            $this->confirm('Opravdu chcete odstranit komentář ' . $subcomment['name'] . '?', [
                'inputAttributes' => [
                    'value' => $id,
                ],
                'onConfirmed' => 'destroySubConfirmed',
                'position' => 'top',
                'cancelButtonText' => 'Zrušit',
                'confirmButtonText' => 'Smazat',
            ]);
        } else {
            $this->showAlert('success', 'Komentář nebyl nalezen.');
        }
    }


    public function createSubcomment($comment_id)
    {
        if (mb_strlen($this->subcommentAdd) > 0) {
            $text = $this->subcommentAdd;
            $author = Auth::id();

            $subcommentAuthor = \App\Models\User::where('id', '=', $author)->first();

            $subcommentAuthorName = $subcommentAuthor->name;

            $subcomment = new \App\Models\SubComment();
            $subcomment['author'] = $subcommentAuthorName;
            $subcomment['text'] = $text;
            $comm = Comment::where('id', $comment_id)->first();
            $subcomment['thread_id'] = $comm->thread_id;
            $subcomment['created_by'] = Auth::id();
            $subcomment['comment_id'] = $comment_id;
            $subcomment->save();
            $this->showAlert('success', 'Komentář přidán!');
            $this->subcommentAdd = '';
        }
        $this->addingSubComment = !$this->addingSubComment;
        $this->emit('update-threads');
    }

    public function toggleAddSub($commentid)
    {
        if($this->addingSubComment == $commentid)
        {
            $this->addingSubComment = -1;
        } else {
            $this->addingSubComment = $commentid;
        }
    }

    public function render()
    {
        return view('livewire.public.thread');
    }
}
