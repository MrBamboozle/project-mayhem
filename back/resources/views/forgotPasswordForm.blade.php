<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset password</title>
</head>
<body style="background-color: #312244; color: white; font-family: Arial, sans-serif; text-align: center; padding: 20px;">
<div style="max-width: 600px; margin: auto; padding: 20px;">
    <h1 style="font-size: 48px; color: #ffcc00; margin-bottom: 50px;">This Form template</h1>
    <form action="{{ route('resetPassword', ['token' => $token]) }}" method="POST">
        @csrf
        @method('POST')
        <label for="password">New Password</label>
        <input type="text" class="form-control" id="password" name="password" value="">
        <button>Change Password</button>
    </form>
</div>
</body>
</html>
