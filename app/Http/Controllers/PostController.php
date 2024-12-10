<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    /**
     * index
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Menampilkan 5 post terbaru
        $posts = Post::latest()->paginate(5);

        return view('posts.index', compact('posts'));
    }

    /**
     * create
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('posts.create');
    }

    /**
     * store
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Validasi input
        $validated = $request->validate([
            'image'     => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'title'     => 'required|min:5',
            'content'   => 'required|min:10',
        ]);

        // Upload gambar
        $imagePath = $request->file('image')->store('public/posts');
        $imageName = basename($imagePath);

        // Membuat post baru
        Post::create([
            'image'     => $imageName,
            'title'     => $validated['title'],
            'content'   => $validated['content'],
        ]);

        return redirect()->route('posts.index')->with('success', 'Post berhasil dibuat!');
    }

    /**
     * edit
     *
     * @param Post $post
     * @return \Illuminate\Http\Response
     */
    public function edit(Post $post)
    {
        return view('posts.edit', compact('post'));
    }

    /**
     * update
     *
     * @param Request $request
     * @param Post $post
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Post $post)
    {
        // Validasi input
        $validated = $request->validate([
            'image'     => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'title'     => 'required|min:5',
            'content'   => 'required|min:10',
        ]);

        // Jika ada gambar baru, upload dan hapus gambar lama
        if ($request->hasFile('image')) {
            // Hapus gambar lama jika ada
            Storage::delete('public/posts/' . $post->image);

            // Upload gambar baru
            $imagePath = $request->file('image')->store('public/posts');
            $imageName = basename($imagePath);

            // Update gambar
            $post->image = $imageName;
        }

        // Update post
        $post->update([
            'title'     => $validated['title'],
            'content'   => $validated['content'],
        ]);

        return redirect()->route('posts.index')->with('success', 'Post berhasil diperbarui!');
    }

    /**
     * destroy
     *
     * @param Post $post
     * @return \Illuminate\Http\Response
     */
    public function destroy(Post $post)
    {
        // Hapus gambar terkait jika ada
        Storage::delete('public/posts/' . $post->image);

        // Hapus post dari database
        $post->delete();

        return redirect()->route('posts.index')->with('success', 'Post berhasil dihapus!');
    }
      public function show($id)
    {
        //get post by ID
        $post = Post::find($id);

        //return view
        return view('posts.show', compact('post'));
    }
    
}
