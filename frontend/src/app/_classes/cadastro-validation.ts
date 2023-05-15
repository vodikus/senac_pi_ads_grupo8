import { AbstractControl, AsyncValidatorFn, ValidationErrors, ValidatorFn } from '@angular/forms';
import { Observable, map, of } from 'rxjs';
import { UsuarioService } from '../_service/usuario.service';

export class CadastroValidation {

    static emailJaExiste(usuarioService: UsuarioService): AsyncValidatorFn {
        return (control: AbstractControl): Observable<ValidationErrors | null> => {
            const email = control.value;
            return usuarioService.validarEmail(email).pipe(
                map( (response) => {
                    return ( response.codigo == 9103) ? { emailJaCadastrado: true } : null
                })
            );
        };
    }

    static cpfJaExiste(usuarioService: UsuarioService): AsyncValidatorFn {
        return (control: AbstractControl): Observable<ValidationErrors | null> => {
            const cpf = control.value;
            return usuarioService.validarCpf(cpf).pipe(
                map( (response) => {
                    return ( response.codigo == 9104) ? { cpfJaCadastrado: true } : null
                })
            );
        };
    }
}
