import { Component } from '@angular/core';

@Component({
    selector: 'my-app',
    styleUrls: [ './app.component.css' ],
    template: `
        <h1>{{title}}</h1>
        <nav>
            <a routerLink="/conditions" routerLinkActive="active">Conditions</a>
            <a routerLink="/contact" routerLinkActive="active">Contact</a>
        </nav>
        <router-outlet></router-outlet>`
})

export class AppComponent  { 
    title = 'Tour of heroes';
}
