import { Controller } from '@hotwired/stimulus'

export default class extends Controller {
    static targets = ["container"]
    static values = {
        url: String
    }

    connect() {
        document.addEventListener('modal:close', ()=> this.hide());
    }

    open() {
        fetch(this.urlValue)
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