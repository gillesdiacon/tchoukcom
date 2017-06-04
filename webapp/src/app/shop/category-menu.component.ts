import { Component, OnInit } from '@angular/core';
import { Router }            from '@angular/router';

import { Category }          from './category';
import { Product }           from './product';
import { ShopService }       from './shop.service';

@Component({
    selector: 'category-menu',
    templateUrl: './category-menu.component.html',
    styleUrls: [ './category-menu.component.css' ],
    providers: [ ShopService ]
})

export class CategoryMenuComponent implements OnInit {

    //for category menu
    categories: Category[];
    selectedCategory: Category;
    
    //in the element (center column)
    categoryElements: Category[];
    productElements: Product[];

    constructor(private shopService: ShopService, private router: Router) {
    }

    ngOnInit(): void {
        this.shopService
            .getCategories(10)
            .then(categories => this.categories = this.categoryElements = categories);
    }
    
    onSelect(category: Category): void {
        this.selectedCategory = category;
        if(category.sub_categories){
            this.categoryElements = category.sub_categories;
            this.productElements = null;
        }else{
            this.categoryElements = null;
            this.shopService.getProducts(category.id).then(products => this.productElements = products);
        }
    }
}