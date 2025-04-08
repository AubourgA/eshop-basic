import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ["radio"];

    connect(){
        const checkedInput = this.element.querySelector('#shipping-1');
        if (checkedInput) {
            this.update({ target: checkedInput });
        }
    }

    update(event) {
        const formData = new FormData();
        formData.append("shippingMethod", event.target.value);
        console.log(formData);
        fetch(`/order/${this.element.dataset.orderId}/shipping`, {
            method: "POST",
            body: formData,
            headers: {
                "X-Requested-With": "XMLHttpRequest"
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Met à jour le total de la commande
                document.getElementById("order-total").textContent = `${data.total}€`;
                
                let labels = document.querySelectorAll("#shipping-form label")
               
                labels.forEach( item => {
                    item.classList.remove('border-green-500');
                })

                let label = document.querySelector(`input[value="${event.target.value}"]+label`)
                
                if(label) {
                    label.classList.add('border-green-500');
                }
            
             

            }
        })
        .catch(error => console.error("Erreur lors de la mise à jour :", error));
    }
}
