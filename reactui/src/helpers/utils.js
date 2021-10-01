export const toAlbelliApiTimeStr = function (date) {
    return date.getUTCFullYear() + "-" + date.getUTCMonth() + "-" + date.getUTCDate() + "T" + date.getUTCHours() + ":" + date.getUTCMinutes() + ":" + date.getUTCSeconds() + "+00:00";
}


export const parseAlbelliApiFields = function (data, dateFields, integerFields) {
    toAlbelliApiTime(data, dateFields);
    toAlbelliNumber(data, integerFields);
}

export const toAlbelliNumber = function (data, integerFields) {
    if (integerFields == null || integerFields === undefined || integerFields.length === 0)
        return;
    integerFields.forEach(df => {
        const pos = Object.keys(data).find(k => k === df);
        if (pos !== undefined) {
            data[pos] = parseInt(data[pos]);
        }
    });
}

export const toAlbelliApiTime = function (data, dateFields) {
    if (dateFields == null || dateFields === undefined || dateFields.length === 0)
        return;
    dateFields.forEach(df => {
        const pos = Object.keys(data).find(k => k === df);
        if (pos !== undefined) {
            data[pos] = data[pos].getUTCFullYear() + "-" + ('0' + (data[pos].getUTCMonth() + 1)).slice(-2) + "-" + ('0' + data[pos].getUTCDate()).slice(-2) + "T" + ('0' + data[pos].getUTCHours()).slice(-2) + ":" + ('0' + data[pos].getUTCMinutes()).slice(-2) + ":" + ('0' + data[pos].getUTCSeconds()).slice(-2) + "+00:00";
        }
    });
}

export const updateCachedData = function (self, data) {
    if (self[self.cacheKey] === null || self[self.cacheKey] === undefined || self[self.cacheKey].length === 0) {
        self[self.cacheKey] = [];
        self[self.cacheKey].push(data);
        return;
    }

    const current = self[self.cacheKey].findIndex((o) => o.id === data.id);
    if (current < 0) {
        self[self.cacheKey].push(data);
    } else {
        self[self.cacheKey][current] = data;
    }
}

export const updateCachedArray = function (self, data) {
    if (Array.isArray(data)) {
        for (var i = 0; i < data.length; i++) {
            updateCachedData(self, data[i]);
        }
    } else {
        updateCachedData(self, data);
    }
}

export const fromAlbelliApiTime = function (data, dateFields) {
    if (dateFields == null || dateFields === undefined || dateFields.length === 0)
        return;
    if (Array.isArray(data)) {
        for (var i = 0; i < data.length; i++) {
            fromAlbelliApiTimeOne(data[i], dateFields);
        }
    } else {
        fromAlbelliApiTimeOne(data, dateFields);
    }
}

export const fromAlbelliApiTimeOne = function (data, dateFields) {
    dateFields.forEach(df => {
        const pos = Object.keys(data).find(k => k === df);
        if (pos !== undefined) {
            data[pos] = new Date(data[pos]);
        }
    });
}

const processAdvertisements = function (offers) {
    if (offers.hasOwnProperty("advertisements") && offers.advertisements != null && offers.advertisements.length > 0) {
        return offers.advertisements.map((data) => ({ "id": data.id, "title": data.title }));
    } else {
        return null;
    }
}

export const returnAdvertisements = function (offers) {
    if (offers == null)
        return offers;
    if (Array.isArray(offers)) {
        return offers.map((adv) => {
            adv.advertisements = processAdvertisements(adv);
            return adv;
        });
    }
}


const processOffers = function (advertisement) {
    if (advertisement.hasOwnProperty("offers") && advertisement.offers != null && advertisement.offers.length > 0) {
        return advertisement.offers.map((data) => ({ "id": data.id, "product_name": data.product_name }));
    } else {
        return null;
    }
}

export const returnOffers = function (advertisements) {
    if (advertisements == null)
        return advertisements;
    if (Array.isArray(advertisements)) {
        return advertisements.map((adv) => {
            adv.offers = processOffers(adv);
            return adv;
        });
    }
}