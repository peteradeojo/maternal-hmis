<?php

namespace App\Http\Controllers\IT;

use LibSQL;
use App\Models\Post;
use App\Enums\Status;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Http\File;

class CrmController extends Controller
{
    public function index(Request $request)
    {
        $posts = Post::latest()->get();

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

        $filename = 'posts/' . strtolower(str_replace(" ", "_", $request->title)) . "_" . date('YmdHis') . '.html';

        if (!is_dir('posts')) {
            if (mkdir('posts') == false) {
                return redirect()->back()->withErrors("Unable to create posts directory.");
            }
        }

        $fh = fopen($filename, "w+");
        if (!$fh) {
            return redirect()->back()->withErrors("Unable to write post content to temp file.");
        }

        $image = app()->isProduction() && !empty($request->file('image')) ? cloudinary()->uploadFile($request->file('image')->getRealPath())->getSecurePath() : $request->file('image')?->store('post_uploads');

        $postText = $request->input('post');
        $post = new Post([
            'user_id' => auth()->user()->id,
            'title' => $request->title,
            'image' => $image,
            'slug' => Str::slug($request->title),
        ]);

        fwrite($fh, $postText);
        fclose($fh);

        $postText = app()->isProduction() ? cloudinary()->uploadFile($filename)->getSecurePath() : Storage::putFileAs('posts', new File($filename, true), $post->slug . ".html");

        $post->post = $postText;
        $post->save();
        unlink($filename);

        return redirect()->route('it.crm-index');
    }

    public function show(Request $request, Post $post)
    {
        $post->load(['user']);

        $postText = app()->isProduction() ? file_get_contents($post->post) : Storage::read($post->post);

        if ($request->expectsJson()) {
            return response()->json([
                'post' => collect($post->toArray())->only([
                    'id',
                    'title',
                    'user.name',
                    'image',
                    'created_at',
                ])->merge(['by' => $post->user->name]),
                'data' => $postText,
            ]);
        }

        return view('it.crm.show', ['post' => $post, 'data' => $postText]);
    }
}
