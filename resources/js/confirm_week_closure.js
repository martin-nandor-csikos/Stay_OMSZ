function confirmWeekClosure(event) {
    event.preventDefault();
    Swal.fire({
        title: 'Hét lezárása',
        text: "Ez egy visszafordíthatatlan esemény. Biztosan le akarod zárni a hetet?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        confirmButtonText: 'Lezárás',
        cancelButtonColor: '#d33',
        cancelButtonText: 'Mégse',
    }).then((result) => {
        if (result.isConfirmed) {
            event.target.form.submit();
        }
    });
}

window.confirmWeekClosure = confirmWeekClosure;
