<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\UserRequest;
use App\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;

class UsersController extends Controller
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return User::latest()->paginate(10);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(UserRequest $request)
    {
        $request['password'] =  Hash::make($request['password']);
        return User::create($request->all());
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'required',
            'email' => "required|unique:users,email,$id"
        ]);
        $user = User::findOrFail($id);
        $user->update($request->all());
        return $user;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        return [ 'message' => 'User was deleted in successfully!'];
    }

    public function profile()
    {
        return auth('api')->user();
    }


    public function profileUpdate(Request $request, $id)
    {
        $user = Auth('api')-> user();
        $this->validate($request, [
            'name' => 'required',
            'email' => "required|unique:users,email,$id",
            'password' => 'sometimes|required|password|min:6'
        ]);

        if($request->photo !== $user->photo) {
            $name = $this->getFilename($request);
            $fullPath = $this->makeFullPath($name);
            $oldFullPath = $this->makeFullPath($user->photo);
            $this->deleteOldPhoto($oldFullPath);
            $this->saveProfilePhoto($request, $fullPath);
            $request->merge(['photo' => $name]);
        }
        $user->update($request->all());
           return ['status' => 'success' ];
    }

    /**
     * @param Request $request
     * @param $extension
     * @return string
     */
    private function getFilename(Request $request)
    {
        $regex = '/^[^\/]+\/([\w]+)/';
        preg_match($regex, $request->photo, $extension);
        return time() . '.' . $extension[1];
    }

    /**
     * @param Request $request
     * @param $fullPath
     */
    private function saveProfilePhoto(Request $request, $fullPath)
    {
        \Image::make($request->photo)->resize(null, 400, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        })->save($fullPath);
    }

    /**
     * @param $fullPath
     */
    private function deleteOldPhoto($fullPath)
    {
        if (file_exists($fullPath)) {
            unlink($fullPath);
        }
    }

    /**
     * @param $name
     * @return mixed
     */
    private function makeFullPath($name)
    {
        return public_path("img/profile/$name");
    }
}
