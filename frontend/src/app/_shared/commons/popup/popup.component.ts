import { Component, Input, Output, EventEmitter } from '@angular/core';

type ChaveValor = {[key: string]: boolean};

@Component({
  selector: 'app-popup',
  templateUrl: './popup.component.html',
  styleUrls: ['./popup.component.scss']
})
export class PopupComponent {
  show: boolean = false;
  @Input('botoes') botoes: ChaveValor = {'cancelar': false, 'ok': true, 'confirmar': false};
  @Input('tipo') tipo: string = "info";
  @Input('titulo') titulo: string = "Aviso";
  @Input('texto') texto: string = "";
  @Output() retorno = new EventEmitter<string>();

  showPopup(): void {
    this.show = true;
  }

  hidePopup(): void {
    this.show = false;
  }

  enviaRetorno(evento: string): void {
    this.retorno.emit(evento);
    this.hidePopup();
  }
  
}
