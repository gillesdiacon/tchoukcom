import { Component, OnInit } from '@angular/core';
import { Router }            from '@angular/router';

@Component({
  selector: 'shop',
  templateUrl: './shop.component.html',
  styleUrls: [ './shop.component.css' ]
})

export class ShopComponent implements OnInit {

    constructor(private router: Router) {
    }

}