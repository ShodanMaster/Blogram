@foreach ($blogs as $blog)
    <div class="card bg-dark mb-3">
        <div class="card-header bg-secondary text-white fs-4">
            {{ $blog->title }}
        </div>
        <div class="card-body text-white text-center">
            {{ $blog->content }}
        </div>
    </div>
@endforeach
