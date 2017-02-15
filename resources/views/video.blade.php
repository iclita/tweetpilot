<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Social Meta Tags -->
    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:site" content="{{ $video->website->url }}" />
    <meta name="twitter:title" content="{{ $video->website->name }}" />
    <meta name="twitter:description" content="{{ $video->title }}" />
    <meta name="twitter:url" content="{{ request()->url() }}" />
    <meta name="twitter:image" content="{{ $video->getImagePreview() }}" />    
</head>
<body>
    <script>
        window.location.href = "{{ route('video.show', ['id' => $video->id]) }}";
    </script>
</body>
</html>