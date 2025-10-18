<?php

namespace App\Http\Controllers\IT;

use App\Enums\Status;
use App\Http\Controllers\Controller;
use App\Models\Post;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use LibSQL;
use Libsql\LibsqlException;

class CrmController extends Controller
{
    public function index(Request $request)
    {
        $db = new LibSQL(config('database.connections.libsql'));
        $posts = $db->query("SELECT * FROM posts")->fetchArray();

        $db->close();

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

        $image = cloudinary()->uploadFile($request->file('image')->getRealPath())->getSecurePath();

        $postText = $request->input('post');
        // $post = new Post([
        //     'user_id' => auth()->user()->id,
        //     'title' => $request->title,
        //     'image' => $image,
        // ]);

        fwrite($fh, $postText);
        fclose($fh);

        $postText = cloudinary()->uploadFile($filename)->getSecurePath();

        // $post->post = $postText;
        // $post->save();

        // $post = [
        //     'user' => auth()->user()->name,
        //     'title' => $request->title,
        //     'image' => $image,
        //     'post' => $postText,
        // ];

        $db = new LibSQL(config('database.connections.libsql'));
        $db->execute("INSERT INTO posts (title, image, post, user, created_at) VALUES (?, ?, ?, ?, ?)", [
            $request->title,
            $image,
            $postText,
            auth()->user()->name,
            now()->format('Y-m-d H:i:s'),
        ]);

        unlink($filename);

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
                    'image',
                    'created_at',
                ])->merge(['by' => $post->user->name]),
                'data' => $postText,
            ]);
        }

        return view('it.crm.show', ['post' => $post, 'data' => $postText]);
    }
}
