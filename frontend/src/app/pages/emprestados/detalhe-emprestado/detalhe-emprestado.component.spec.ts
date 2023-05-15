import { ComponentFixture, TestBed } from '@angular/core/testing';

import { DetalheEmprestadoComponent } from './detalhe-emprestado.component';

describe('DetalheEmprestadoComponent', () => {
  let component: DetalheEmprestadoComponent;
  let fixture: ComponentFixture<DetalheEmprestadoComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [ DetalheEmprestadoComponent ]
    })
    .compileComponents();

    fixture = TestBed.createComponent(DetalheEmprestadoComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
