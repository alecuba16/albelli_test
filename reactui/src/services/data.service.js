import axios from "axios";
import authHeader from "./auth-header";
import { parseAlbelliApiFields, fromAlbelliApiTime, updateCachedArray } from './../helpers/utils';
import { API_URL } from './../configs';

class DataService {
    constructor(type) {
        if (type === "offers") {
            this.cacheKey = "offers";
            this.endPoint = "offers";
            this.dateFields = ['start_date', 'end_date'];
            this.integerFields = ['discount_value'];
            this.relatedKey = "advertisements";
            this[this.cacheKey] = null;
        } else {
            this.cacheKey = "advertisements";
            this.endPoint = "advertisements";
            this.dateFields = [];
            this.integerFields = [];
            this.relatedKey = "offers";
            this[this.cacheKey] = null;
        }
    }

    getAll() {
        var self = this;
        if (self[self.cacheKey] != null) {
            return new Promise((resolve, reject) => {
                resolve(self[self.cacheKey]);
            });
        } else {
            return axios
                .get(API_URL + this.endPoint, { headers: authHeader() })
                .then((response) => {
                    if (response.data.data && response.data) {
                        const elements = response.data.data;
                        fromAlbelliApiTime(elements, self.dateFields);
                        updateCachedArray(self, elements);
                        return elements;
                    } else {
                        return [];
                    }
                });
        }
    }

    getOne(id) {
        var self = this;
        if (self[self.cacheKey] != null) {
            return new Promise((resolve, reject) => {
                resolve(self[self.cacheKey].find((o) => o.id === id));
            });
        } else {
            return axios
                .get(API_URL + this.endPoint + "/" + id, { headers: authHeader() })
                .then((response) => {
                    if (response.data.data && response.data) {
                        const element = response.data.data;
                        fromAlbelliApiTime(element, self.dateFields);
                        updateCachedArray(self, element);
                        return [element];
                    } else {
                        return [];
                    }
                });
        }
    }

    addOne(element) {
        var self = this;
        parseAlbelliApiFields(element, self.dateFields, self.integerFields);
        return axios
            .post(API_URL + this.endPoint, element, { headers: authHeader() })
            .then((response) => {
                if (response.data.data && response.data) {
                    const element = response.data.data;
                    fromAlbelliApiTime(element, self.dateFields);
                    updateCachedArray(self, element);
                }
                return self[self.cacheKey];
            });
    }

    updateOne(element) {
        var self = this;
        parseAlbelliApiFields(element, self.dateFields, self.integerFields);
        return axios
            .put(API_URL + this.endPoint + "/" + element.id, element, { headers: authHeader() })
            .then((response) => {
                if (response.data.data && response.data) {
                    var element = response.data.data;
                    fromAlbelliApiTime(element, self.dateFields);
                    updateCachedArray(self, element);
                    return self[self.cacheKey];
                } else {
                    return [];
                }
            });
    }


    deleteOneById(elementId) {
        var self = this;
        return axios
            .delete(API_URL + this.endPoint + "/" + elementId, { headers: authHeader() })
            .then((response) => {
                if (response.data.success) {
                    const current = self[self.cacheKey].findIndex((o) => o.id === elementId);
                    if (current >= 0) {
                        self[self.cacheKey].splice(current, 1);
                    }
                }
                return self[self.cacheKey];
            });
    }

    deleteOneRelatedById(elementId, relatedId) {
        var self = this;
        var currentParent = self[self.cacheKey].findIndex((o) => o.id === elementId);
        if (currentParent < 0)
            return Promise.reject({ message: `Parent id ${elementId} doesnt exists` });
        currentParent = self[self.cacheKey][currentParent];
        const currentRelated = currentParent[self.relatedKey].findIndex((o) => o.id === relatedId);
        if (currentRelated < 0)
            return Promise.reject({ message: `Related id ${relatedId} doesnt exists in the parent id ${elementId}` });

        currentParent[self.relatedKey] = currentParent[self.relatedKey].filter((o) => o.id !== relatedId);
        return axios
            .put(API_URL + this.endPoint + "/" + elementId, currentParent, { headers: authHeader() })
            .then((response) => {
                return self[self.cacheKey];
            });
    }
}



export const OfferService = new DataService("offers");
export const AdvertisementService = new DataService("advertisements");
