import { ComponentFixture, TestBed } from '@angular/core/testing';

import { CaixaUsuarioComponent } from './caixa-usuario.component';

describe('CaixaUsuarioComponent', () => {
  let component: CaixaUsuarioComponent;
  let fixture: ComponentFixture<CaixaUsuarioComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [ CaixaUsuarioComponent ]
    })
    .compileComponents();

    fixture = TestBed.createComponent(CaixaUsuarioComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
