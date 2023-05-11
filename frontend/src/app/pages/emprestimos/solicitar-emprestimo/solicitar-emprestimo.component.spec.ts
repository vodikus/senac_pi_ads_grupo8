import { ComponentFixture, TestBed } from '@angular/core/testing';

import { SolicitarEmprestimoComponent } from './solicitar-emprestimo.component';

describe('SolicitarEmprestimoComponent', () => {
  let component: SolicitarEmprestimoComponent;
  let fixture: ComponentFixture<SolicitarEmprestimoComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [ SolicitarEmprestimoComponent ]
    })
    .compileComponents();

    fixture = TestBed.createComponent(SolicitarEmprestimoComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
