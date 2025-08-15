function swalConfirmDecision(event, title, text, confirmButtonText, cancelButtonText) {
    event.preventDefault();
    Swal.fire({
        title: title,
        text: text,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        confirmButtonText: confirmButtonText,
        cancelButtonColor: '#d33',
        cancelButtonText: cancelButtonText,
    }).then((result) => {
        if (result.isConfirmed) {
            event.target.form.submit();
        }
    });
}

window.swalConfirmDecision = swalConfirmDecision;
