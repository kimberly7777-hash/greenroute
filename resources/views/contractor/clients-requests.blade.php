@extends('layouts.app')

@section('content')
<div class="container">
    <h3>Pending Service Requests</h3>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($requests->isEmpty())
        <p>No pending service requests.</p>
    @else
        <table class="table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Phone</th>
                    <th>Email</th>
                    <th>Address</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($requests as $r)
                    <tr>
                        <td>{{ $r->id }}</td>
                        <td>{{ $r->name }}</td>
                        <td>{{ $r->phone }}</td>
                        <td>{{ $r->email }}</td>
                        <td>{{ $r->address }}</td>
                        <td>
                            <form method="POST" action="{{ route('contractor.clients.requests.accept', $r->id) }}" style="display:inline">
                                @csrf
                                <button class="btn btn-sm btn-success">Accept</button>
                            </form>

                            <form method="POST" action="{{ route('contractor.clients.requests.reject', $r->id) }}" style="display:inline" class="ms-2">
                                @csrf
                                <input type="text" name="rejection_reason" placeholder="Reason (optional)" class="form-control form-control-sm d-inline-block" style="width:220px; display:inline-block">
                                <button class="btn btn-sm btn-danger ms-1">Reject</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
@endsection
