import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['panel', 'overlay'];

    connect() {
        // Optionnel : console.log pour vérifier que le controller est actif
        console.log('Filter controller connected');
    }

    open() {
        
        this.panelTarget.classList.remove('translate-x-full');
        this.overlayTarget.classList.remove('hidden');
    }

    close() {
        this.panelTarget.classList.add('translate-x-full');
        this.overlayTarget.classList.add('hidden');
    }
}