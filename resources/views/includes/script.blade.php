<script src="{{ url('assets/vendor/jquery/jquery.min.js') }}"></script>
<script src="{{ url('assets/vendor/jquery/jquery-1.12.4.js') }}"></script>
<script src="{{ url('assets/vendor/jquery/jquery-ui.js') }}"></script>
<script src="{{ url('assets/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ url('assets/vendor/jquery-easing/jquery.easing.min.js') }}"></script>
<script src="{{ url('assets/js/sb-admin-2.js') }}"></script>
<script type="text/javascript">
    $(document).ready(function() {
        $('#menuProduct').on('click', function(event) {
            event.preventDefault();

            let sourceMenu = $(this).attr('href');
            $('#targetRoute').val(sourceMenu);
            $('#modalPassword').modal('show');
        });

        $('#togglePassword').on('click', function(e) {
            let password = document.getElementById('password');
            let type = password.getAttribute('type') === 'password' ? 'text' : 'password';

            password.setAttribute('type', type);
            this.classList.toggle('fa-eye-slash');
        });

        $('#btnSubmitPassword').on('click', function(event) {
            event.preventDefault();

            $('#passwordErrorMessage').css('display', 'none');
            let password = $('#password').val();

            $.ajax({
                url: '{{ route('validate-password-ajax') }}',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    password: password
                },
                dataType: 'json',
                success: function(data) {
                    $('#password').val('');
                    $('#modalPassword').modal('hide');
                    window.location.href = $('#targetRoute').val();
                },
                error: function(xhr) {
                    if(xhr.status === 422) {
                        let errors = xhr.responseJSON;
                        $('#passwordErrorMessage').text(errors.message).css('display', 'block');
                    } else {
                        $('#passwordErrorMessage').text('An error occurred. Please try again.');
                    }
                }
            })
        });
    });
</script>
