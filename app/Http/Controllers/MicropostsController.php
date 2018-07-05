<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;

class MicropostsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = [];
        if (\Auth::check()) {
            $user = \Auth::user();
           $microposts = $user->feed_microposts()->orderBy('created_at', 'desc')->paginate(10);
            $data = [
                'user' => $user,
                'microposts' => $microposts,
            ];
        }
        
        return view('welcome', $data);
    }

    public function store(Request $request)
    {
        //return var_dump($request->file);
        //return print $request->file;

        $this->validate($request, [
            'content' => 'required|max:191',
            'file' => [
            // アップロードされたファイルであること
            'file',
            // 最小縦横120px 最大縦横400px
            'dimensions:min_width=10,min_height=10,max_width=2400,max_height=2400',
            ] 
        ]);
        if (null !== $request->file){
            if ($request->file('file')->isValid([])) {
                $filename = basename($request->file->store('public/image'));
                $request->user()->microposts()->create([
                    'content' => $request->content,
                    'image_url' => $filename,
                ]);
            }
        }else{
            $request->user()->microposts()->create([
                'content' => $request->content,
            ]);
        }
        return redirect()->back();
    }

    public function destroy($id)
    {
        
        $micropost = \App\Micropost::find($id);

        if (\Auth::id() === $micropost->user_id) {
            $micropost->delete();
        }

        return redirect()->back();
    }
}