<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Transformers\UserTransformer;
use Illuminate\Http\Request;
use League\Fractal\Manager;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use League\Fractal\Resource\Collection;
use League\Fractal\Serializer\ArraySerializer;

class UsersController extends Controller
{
    /**
     * @var Manager
     */
    private $fractal;

    /**
     * @var UserTransformer
     */
    private $userTransformer;

    function __construct(Manager $fractal, UserTransformer $userTransformer, ArraySerializer $serializer)
    {
        $this->fractal = $fractal;
        $this->userTransformer = $userTransformer;
        $this->fractal->setSerializer($serializer);

    }

    public function index(Request $request)
    {


        $usersPaginator = User::paginate(10);
        $users = new Collection($usersPaginator->items(), $this->userTransformer, 'Users');

        $users->setPaginator(new IlluminatePaginatorAdapter($usersPaginator));

        $this->fractal->parseIncludes($request->get('include', '')); // parse includes

        return $this->fractal->createData($users)->toJson();

    }


    public function show($id)
    {

        return User::findOrFail($id);
    }

    public function store(Request $request)
    {
        return User::create($request->all());
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $user->update($request->all());

        return $user;
    }

    public function delete(Request $request, $id)
    {
        $article = User::findOrFail($id);
        $article->delete();

        return 204;
    }
}