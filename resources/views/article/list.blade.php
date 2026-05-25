@extends('layouts.app1')

@section('content')
    <!-- DataTales Example -->
    <div class="card shadow mb-4 bg-white">
        <div class="card-header py-3 border-b px-4">
            <h6 class="m-0 font-weight-bold text-primary font-semibold text-blue-600">DataTables Example</h6>
        </div>
        <div class="card-body p-4">
            <div class="table-responsive overflow-x-auto">
                <table class="table table-bordered min-w-full border border-gray-200" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                    <tr class="bg-gray-50">
                        <th class="border px-4 py-2 text-left">Title</th>
                        <th class="border px-4 py-2 text-left">User</th>
                        <th class="border px-4 py-2 text-left">Body</th>
                        <th class="border px-4 py-2 text-left">Create At</th>
                        <th class="border px-4 py-2 text-left">Tags</th>
                        <th class="border px-4 py-2 text-left">Edit</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($articles as $article)
                        <tr>
                            <td class="border px-4 py-2">{{ $article->title }}</td>
                            <td class="border px-4 py-2"><a class="text-blue-600" href="/profiles/{{ $article->user_id }}">{{ $article->user->name }}</a></td>
                            <td class="border px-4 py-2">{{ $article->body }}</td>
                            <td class="border px-4 py-2">{{ $article->created_at }}</td>
                            <td class="border px-4 py-2">
                                @foreach($article->tags as $tag)
                                    <a class="text-blue-600" href="#">{{ $tag->tag }} </a> ,
                                @endforeach
                            </td>
                            <th class="border px-4 py-2 text-left">Edit</th>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
