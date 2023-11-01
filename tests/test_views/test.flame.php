<!DOCTYPE html>
<html lang="en">
<head>
     <meta charset="UTF-8">
     <meta name="viewport" content="width=device-width, initial-scale=1.0">
     <title>Test Document</title>
</head>
<body>
     @if($condition)
          Hello {{ $dev }}
     @else:
          Hello {{ $user }}
     @endif
</body>
</html>