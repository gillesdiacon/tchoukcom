import { NgModule }      from '@angular/core';
import { BrowserModule } from '@angular/platform-browser';
import { FormsModule }   from '@angular/forms';
import { HttpModule }    from '@angular/http';
import { RouterModule }   from '@angular/router';

import { AppRoutingModule }     from './app-routing.module';

// Imports for loading & configuring the in-memory web api
import { InMemoryWebApiModule } from 'angular-in-memory-web-api';
import { InMemoryDataService }  from './in-memory-data.service';

import { AppComponent }  from './app.component';
import { ConditionsComponent } from './conditions.component';
import { ContactComponent } from './contact.component';
import { FooterComponent } from './footer.component';
import { HeroDetailComponent } from './hero-detail.component';

import { HeroService } from './hero.service';

@NgModule({
    imports: [ 
        BrowserModule,
        FormsModule,
        HttpModule,
        InMemoryWebApiModule.forRoot(InMemoryDataService),
        AppRoutingModule
    ],
    declarations: [ AppComponent, ConditionsComponent, ContactComponent, FooterComponent, HeroDetailComponent ],
    providers:    [ HeroService ],
    bootstrap:    [ AppComponent ]
})
export class AppModule { }
