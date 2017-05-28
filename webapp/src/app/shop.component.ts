import { Component, OnInit } from '@angular/core';
import { Router }            from '@angular/router';

import { Category }          from './category';
import { ShopService }       from './shop.service';

@Component({
  selector: 'shop',
  templateUrl: './shop.component.html',
  styleUrls: [ './shop.component.css' ],
  providers: [ ShopService ]
})

export class ShopComponent implements OnInit {

    categories: Category[];
    selectedCategory: Category;
    parentSelectedCategory: Category;

    constructor(private shopService: ShopService, private router: Router) {
    }

    getCategories(): void {
        this.shopService
            .getCategories()
            .then(categories => this.categories = categories);
    }

    ngOnInit(): void {
        this.getCategories();
    }
    
    onSelect(category: Category): void {
        console.log(category);
    
        this.selectedCategory = category;
        this.parentSelectedCategory = category;
    }
    
    onSubSelect(category: Category, subCategory: Category): void {
        console.log("onSubSelect");
        
        this.selectedCategory = category;
        this.parentSelectedCategory = subCategory;
    }
}