import { ComponentFixture, TestBed } from '@angular/core/testing';

import { CaixaLivroComponent } from './caixa-livro.component';

describe('CaixaLivroComponent', () => {
  let component: CaixaLivroComponent;
  let fixture: ComponentFixture<CaixaLivroComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [ CaixaLivroComponent ]
    })
    .compileComponents();

    fixture = TestBed.createComponent(CaixaLivroComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
