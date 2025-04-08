import { Controller } from '@hotwired/stimulus'

export default class extends Controller {
    static targets = ["display", "phone", "form", "input", "success", "error"]

 

    showForm() {
        this.displayTarget.classList.add("hidden")
        this.phoneTarget.classList.add("hidden")
        this.formTarget.classList.remove("hidden")
        this.successTarget.classList.add("hidden")
        this.errorTarget.classList.add("hidden")
    }

    async submit(event) {
        event.preventDefault()

        const reponse = await fetch(this.formTarget.action, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify( {phone: this.inputTarget.value})
        })

   

       if(reponse.ok) {
        const data = await reponse.json()
        this.formTarget.innerHTML = ` ${data.phone}  `        
        this.successTarget.classList.remove("hidden")
        this.errorTarget.classList.add("hidden")
       } else {
               this.successTarget.classList.add("hidden")
               this.errorTarget.classList.remove("hidden")
       }

    }
}