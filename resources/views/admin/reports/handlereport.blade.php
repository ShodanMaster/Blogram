@extends('admin.layout')
@section('content')

<div class="card shadow-lg">
    <div class="card-header bg-danger text-center text-white fs-1">Handle Report</div>
        <div class="card-body">
            <table class="table" border="1">
                <tr>
                    <th>Reason</th>
                    <td>{{$report->reason}}</td>
                </tr>
                <tr>
                    <th>Content Type</th>
                    <td>{{ class_basename($report->reportable_type) }}</td>
                </tr>
                <tr>
                    <th>Content</th>
                    <td>

                        @if ($report->reportable_type === 'App\Models\Blog')
                            <a href="{{ route('admin.converstaions', encrypt($report->reportable_id)) }}" target="_blank">
                                {{ $report->reportable->title }}
                            </a>
                        @elseif ($report->reportable_type === 'App\Models\User')
                            <a href="{{ route('admin.userprofile', encrypt($report->user_id)) }}" target="_blank">
                                {{ $report->reportable->name }}
                            </a>
                        @elseif ($report->reportable_type === 'App\Models\Comment')

                            @if ($report->reportable && !$report->reportable->comment)
                                <span class="text-danger">Comment Was Removed</span>
                            @elseif ($report->reportable)
                                {{ Str::limit($report->reportable->comment, 50) }}
                            @else
                                <span class="text-danger">No content available</span>
                            @endif

                        @endif
                    </td>
                </tr>
                <tr>
                    <th>Status</th>
                    <td>
                        @if ($report->reportable_type === 'App\Models\Comment' && !$report->reportable)
                            resolved
                        @else
                            {{$report->status}}
                        @endif
                    </td>
                </tr>
            </table>
    <form action="{{route('admin.reporthandled', $report)}}" method="POST">
        @csrf
        <div class="form-group">
            <select class="form-control" name="status" id="status" required>
                <option value="" selected disabled>-- select status --</option>
                <option value="pending">pending</option>
                <option value="resolved">resolved</option>
                <option value="dismissed">dismissed</option>
            </select>
        </div>
        </div>
        <div class="card-footer d-flex justify-content-end">
            <button class="btn btn-danger">Submit</button>
        </div>
    </form>
</div>

@endsection
