import { Component, OnInit } from '@angular/core';
import { Router }            from '@angular/router';

import { Category }          from './category';
import { ShopService }       from './shop.service';

@Component({
    selector: 'category-menu',
    templateUrl: './category-menu.component.html',
    styleUrls: [ './category-menu.component.css' ],
    providers: [ ShopService ]
})

export class CategoryMenuComponent implements OnInit {

    categories: Category[];
    selectedCategory: Category;

    constructor(private shopService: ShopService, private router: Router) {
    }

    ngOnInit(): void {
        this.shopService
            .getCategory(10)
            .then(rootCategory => this.categories = rootCategory.sub_categories);
    }
    
    onSelect(category: Category): void {
        this.selectedCategory = category;
    }
}