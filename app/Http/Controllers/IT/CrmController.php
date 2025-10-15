<?php

namespace App\Http\Controllers\IT;

use App\Enums\Status;
use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CrmController extends Controller
{
    public function index(Request $request)
    {
        $posts = Post::select(['title', 'id', 'created_at', 'status'])
            ->where('status', Status::active->value)->latest()->get();

        if ($request->expectsJson()) {
            return response()->json($posts);
        }

        return view('it.crm.index', compact('posts'));
    }

    public function create(Request $request)
    {
        return view('it.crm.create');
    }

    public function publish(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'description' => 'string|nullable',
            'post' => 'required|string',
            'image' => 'required|image'
        ]);

        $image = $request->file('image')?->store('post_uploads');
        $postText = $request->input('post');
        $post = new Post([
            'user_id' => auth()->user()->id,
            'title' => $request->title,
            'image' => $image,
        ]);

        $filename = 'posts/' . strtolower(str_replace(" ", "_", $request->title)) . "_" . date('YmdHis') . '.html';
        $postText = Storage::write($filename, $postText);

        $post->post = $filename;
        $post->save();

        return redirect()->route('it.crm-index');
    }

    public function show(Request $request, Post $post)
    {
        $post->load(['user']);

        $postText = Storage::read($post->post);

        if ($request->expectsJson()) {
            return response()->json([
                'post' => collect($post->toArray())->only([
                    'id',
                    'title',
                    'user.name',
                    'created_at',
                ])->merge(['by' => $post->user->name]),
                'data' => $postText,
            ]);
        }

        return view('it.crm.show', ['post' => $post, 'data' => $postText]);
    }
}
