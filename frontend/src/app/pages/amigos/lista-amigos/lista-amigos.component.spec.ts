import { ComponentFixture, TestBed } from '@angular/core/testing';

import { ListaAmigosComponent } from './lista-amigos.component';

describe('ListaAmigosComponent', () => {
  let component: ListaAmigosComponent;
  let fixture: ComponentFixture<ListaAmigosComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [ ListaAmigosComponent ]
    })
    .compileComponents();

    fixture = TestBed.createComponent(ListaAmigosComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
