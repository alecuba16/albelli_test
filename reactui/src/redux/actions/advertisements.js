import {
  ADVERTISEMENTS_UPDATED,
  SET_MESSAGE,
  LOGOUT
} from "./types";

import { AdvertisementService } from "../../services/data.service";


export const getAdvertisements = () => (dispatch) => {
  return AdvertisementService.getAll().then(
    (response) => {
      dispatch({
        type: ADVERTISEMENTS_UPDATED,
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

export const getAdvertisement = (id) => (dispatch) => {
  return AdvertisementService.getOne(id).then(
    (response) => {
      dispatch({
        type: ADVERTISEMENTS_UPDATED,
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

export const deleteAdvertisementById = (advertisementId) => (dispatch) => {
  return AdvertisementService.deleteOneById(advertisementId).then(
    (response) => {
      dispatch({
        type: ADVERTISEMENTS_UPDATED,
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

export const addAdvertisement = (advertisement) => (dispatch) => {
  return AdvertisementService.addOne(advertisement).then(
    (response) => {
      dispatch({
        type: ADVERTISEMENTS_UPDATED,
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

export const updateAdvertisement = (advertisement) => (dispatch) => {
  return AdvertisementService.updateOne(advertisement).then(
    (response) => {
      dispatch({
        type: ADVERTISEMENTS_UPDATED,
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
  return AdvertisementService.deleteOneRelatedById(elementId, relatedId).then(
    (response) => {
      dispatch({
        type: ADVERTISEMENTS_UPDATED,
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



