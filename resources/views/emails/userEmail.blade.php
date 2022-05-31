<!DOCTYPE html>
<html>
<head>
    <title>Email From admin</title>
</head>
<body>
  
    <h1>Hi, {{ $user->name }}</h1>
    <p>{{ $user->email }}</p>
    
    <p>{{ $body }}</p>
     
    <p>Thank you</p>
</body>
</html>