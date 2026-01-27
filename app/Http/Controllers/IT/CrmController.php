<?php

namespace App\Http\Controllers\IT;

use App\Enums\Permissions;
use App\Models\Post;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Media;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\File;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules\File as RulesFile;

class CrmController extends Controller
{
    public function index(Request $request)
    {
        try {
            $posts = Post::latest()->get();

            if ($request->expectsJson()) {
                return response()->json($posts);
            }

            return view('it.crm.index', compact('posts'));
        } catch (\Throwable $th) {
            return abort(500, $th->getMessage());
        }
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
            'user' => auth()->user()->name,
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

        $postText = file_get_contents($post->post); // : Storage::read($post->post);

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

    public function updatePostStatus(Request $request, Post $post)
    {
        if ($request->user()->can(Permissions::EDIT_POSTS->value, $post) == false) {
            return abort(403);
        }

        $data = $request->validate([
            'status' => 'required|integer',
        ]);

        $post->status = $data['status'];
        $post->save();

        return redirect()->back();
    }

    public function dropbox(Request $request)
    {
        if (!$request->isMethod('POST')) {
            $departments = Department::all()->toArray();
            if ($request->has('fetch')) {
                $query = Media::accessible($request->user())->active()->latest();
                return $this->dataTable($request, $query);
            }

            return view('dropbox', compact('departments'));
        }

        $request->validate([
            'to' => 'required|in:user,dept',
            'file' => 'required|file|mimes:png,jpg,pdf,docx,xlsx,xls,doc,odt|max:' . (20 * 1024),
            'phone' => 'required_if:to,user|exists:users,phone',
            'department' => 'required_if:to,dept',
        ]);

        if ($request->input('to') == 'user') {
            $receiver = User::where('phone', $request->input('phone'))->first();
        } else {
            if ($request->input('department') == 'all') {
                $receiver = null;
            } else {
                $receiver = Department::where('name', $request->input('dept'))->first();
            }
        }

        $file = $request->file('file');

        DB::beginTransaction();

        try {
            $entry = new Media([
                'user_id' => $request->user()->id,
                'size' => $file->getSize(),
                'file_type' => $file->getClientMimeType(),
                'file_name' => $file->getClientOriginalName(),
                'medially_type' => User::class,
                'medially_id' => $request->user()->id,
                'expires_at' => null,
                'receiver_type' => !empty($receiver) ? $receiver::class : null,
                'receiver_id' => !empty($receiver) ? $receiver->id : null,
                'file_url' => $file->store('media'),
            ]);
            $entry->save();

            DB::commit();
            return redirect()->back();
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->back()->withErrors($th->getMessage());
        }
    }

    public function downloadMedia(Request $request, Media $entry)
    {
        return Storage::download($entry->file_url, $entry->file_name);
    }
}
