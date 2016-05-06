<!-- resources/views/auth/login.blade.php -->
<link rel="stylesheet" type="text/css" href="/css/bootstrap.min.css">
<link rel="stylesheet" type="text/css" href="/css/bootstrap-theme.min.css">
<link rel="stylesheet" type="text/css" href="/css/style.css">
<style>
    body {
        background-color: #9d9d9d;
    }
    fieldset {width: 15%;
    padding: 30px;
    background-color: #ffffff;
    margin: 0 auto;}
</style>
<form method="POST" action="/login">
    {!! csrf_field() !!}
    <fieldset >
        <div class="form-group">
            <label for="disabledTextInput">Email</label>
            <input  type="email" name="email" value="{{ old('email') }}" class="form-control" placeholder="Disabled input">
        </div>
        <div class="form-group">
            <label for="disabledTextInput">Pass</label>
            <input class="form-control" type="password" name="password" id="password">
        </div>

        <div class="checkbox">
            <label>
                <input type="checkbox" name="remember"> Remember me
            </label>
        </div>
        <button type="submit" class="btn btn-success">Sign In</button>
    </fieldset>
</form>