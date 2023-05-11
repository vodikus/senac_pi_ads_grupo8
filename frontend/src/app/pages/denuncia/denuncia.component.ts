import { Component, OnInit } from '@angular/core';
import { ActivatedRoute } from '@angular/router';

@Component({
  selector: 'app-denuncia',
  templateUrl: './denuncia.component.html',
  styleUrls: ['./denuncia.component.scss']
})
export class DenunciaComponent implements OnInit {
  uid: number = 0;

  constructor(
    private route: ActivatedRoute
  ) { }

  ngOnInit(): void {
    this.uid = Number(this.route.snapshot.paramMap.get('id'));
  }

}
