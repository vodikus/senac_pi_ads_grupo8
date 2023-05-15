import { ComponentFixture, TestBed } from '@angular/core/testing';

import { ListarEmprestadosComponent } from './listar-emprestados.component';

describe('ListarEmprestadosComponent', () => {
  let component: ListarEmprestadosComponent;
  let fixture: ComponentFixture<ListarEmprestadosComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [ ListarEmprestadosComponent ]
    })
    .compileComponents();

    fixture = TestBed.createComponent(ListarEmprestadosComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
