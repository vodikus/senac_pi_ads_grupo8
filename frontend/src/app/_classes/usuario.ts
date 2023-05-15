export class Usuario {
    uid: number = 0;
    nome: string = '';
    apelido: string = '';
    avatar: string = '';
    amigo: number = 0;
    bloqueado: number = 0;
    ultimo_login: Date = new Date();
    status_chat: number = 0;
}
