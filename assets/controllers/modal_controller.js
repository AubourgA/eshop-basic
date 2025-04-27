import { Controller } from '@hotwired/stimulus'

export default class extends Controller {
    static targets = ["container"]
    static values = {
        url: String
    }

    connect() {
        document.addEventListener('modal:close', ()=> this.hide());
    }

    open(event) {

        let url = this.urlValue; // par défaut
        if (event?.currentTarget?.dataset?.modalUrlValue) {
            url = event.currentTarget.dataset.modalUrlValue; // utilise l'url du bouton cliqué s'il existe
        }

        fetch(url)
            .then(response => response.text())
            .then(html => {
                this.containerTarget.innerHTML = html
            })
    }

    close(event) {
        event.preventDefault()
        this.containerTarget.innerHTML = ''
    }

    hide() {
        this.containerTarget.innerHTML = '';
    }
}