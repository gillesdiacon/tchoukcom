import { Component, OnInit } from '@angular/core';
import { Router }            from '@angular/router';

@Component({
  selector: 'hero-search',
  templateUrl: './footer.component.html',
  styleUrls: [ './footer.component.css' ]
})

export class FooterComponent implements OnInit {

    constructor(private router: Router) {
    }

}