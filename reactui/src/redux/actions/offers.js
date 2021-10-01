import {
  OFFERS_UPDATED,
  SET_MESSAGE,
  LOGOUT
} from "./types";

import { OfferService } from "../../services/data.service";

export const getOffers = () => (dispatch) => {
  return OfferService.getAll().then(
    (response) => {
      dispatch({
        type: OFFERS_UPDATED,
        payload: response
      });

      return Promise.resolve(response);
    },
    (error) => {
      const message =
        (error.response &&
          error.response.data &&
          error.response.data.message) ||
        error.message ||
        error.toString();
      if (error.response && error.response.status === 401) {
        dispatch({
          type: LOGOUT,
        });
      }
      dispatch({
        type: SET_MESSAGE,
        payload: message,
      });

      return Promise.reject(message);
    }
  );
};

export const getOffer = (id) => (dispatch) => {
  return OfferService.getOne(id).then(
    (response) => {
      dispatch({
        type: OFFERS_UPDATED,
        payload: response
      });

      return Promise.resolve(response);
    },
    (error) => {
      const message =
        (error.response &&
          error.response.data &&
          error.response.data.message) ||
        error.message ||
        error.toString();
      if (error.response && error.response.status === 401) {
        dispatch({
          type: LOGOUT,
        });
      }
      dispatch({
        type: SET_MESSAGE,
        payload: message,
      });

      return Promise.reject(message);
    }
  );
};

export const deleteOfferById = (offerId) => (dispatch) => {
  return OfferService.deleteOneById(offerId).then(
    (response) => {
      dispatch({
        type: OFFERS_UPDATED,
        payload: response
      });

      return Promise.resolve(response);
    },
    (error) => {
      const message =
        (error.response &&
          error.response.data &&
          error.response.data.message) ||
        error.message ||
        error.toString();
      if (error.response && error.response.status === 401) {
        dispatch({
          type: LOGOUT,
        });
      }
      dispatch({
        type: SET_MESSAGE,
        payload: message,
      });

      return Promise.reject(message);
    }
  );
};

export const addOffer = (offer) => (dispatch) => {
  return OfferService.addOne(offer).then(
    (response) => {
      dispatch({
        type: OFFERS_UPDATED,
        payload: response
      });

      return Promise.resolve(response);
    },
    (error) => {
      const message =
        (error.response &&
          error.response.data &&
          error.response.data.message) ||
        error.message ||
        error.toString();
      if (error.response && error.response.status === 401) {
        dispatch({
          type: LOGOUT,
        });
      }
      dispatch({
        type: SET_MESSAGE,
        payload: message,
      });

      return Promise.reject(message);
    }
  );
};

export const updateOffer = (offer) => (dispatch) => {
  return OfferService.updateOne(offer).then(
    (response) => {
      dispatch({
        type: OFFERS_UPDATED,
        payload: response
      });

      return Promise.resolve(response);
    },
    (error) => {
      const message =
        (error.response &&
          error.response.data &&
          error.response.data.message) ||
        error.message ||
        error.toString();
      if (error.response && error.response.status === 401) {
        dispatch({
          type: LOGOUT,
        });
      }
      dispatch({
        type: SET_MESSAGE,
        payload: message,
      });

      return Promise.reject(message);
    }
  );
};

export const deleteOneRelatedById = (elementId, relatedId) => (dispatch) => {
  return OfferService.deleteOneRelatedById(elementId, relatedId).then(
    (response) => {
      dispatch({
        type: OFFERS_UPDATED,
        payload: response
      });

      return Promise.resolve(response);
    },
    (error) => {
      const message =
        (error.response &&
          error.response.data &&
          error.response.data.message) ||
        error.message ||
        error.toString();
      if (error.response && error.response.status === 401) {
        dispatch({
          type: LOGOUT,
        });
      }
      dispatch({
        type: SET_MESSAGE,
        payload: message,
      });

      return Promise.reject(message);
    }
  );
};


