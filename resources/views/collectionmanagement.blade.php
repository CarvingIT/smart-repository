@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Add Collection</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                   <form method="post" action="/admin/savecollection">
                    @csrf()
                   <div class="input-with-label">
                   <label for="collection_name">Name</label> 
                   <input type="text" name="collection_name" id="collection_name" value="" placeholder="Give your collection a name" />
                   </div>
                   <div class="input-with-label">
                   <label for="description">Description</label> 
                   <textarea id="description" name="description" placeholder="Description of the collecton here"></textarea>
                   </div>
                   <div>
                   <input type="checkbox" name="collection_type" value="Members Only" /> Members Only
                   </div>
                   <div class="input-with-label">
                   <label for="maintainer">Maintainer</label> 
                   <input type="text" name="maintainer" id="maintainer" value="" placeholder="Enter maintainer's ID" />
                   </div>
                   <input type="submit" value="Submit" />
                   </form> 
                </div>
            </div>

            <div class="card">
                <div class="card-header">Collections</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <table>
                        <thead>
                            <th>Name</th>
                            <th>Actions</th>
                        </thead>
                        @foreach ($collections as $c)
                        <tr>
                            <td>{{ $c->name }}</td>
                            <td>e x</td>    <!-- use font awesome icons or image icons -->
                        </tr>
                        @endforeach
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
