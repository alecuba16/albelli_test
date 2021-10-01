import {
  OFFERS_UPDATED,
  ADVERTISEMENTS_UPDATED
} from "../actions/types";

const initialState = { offers: null, advertisements: null };

export default function (state = initialState, action) {
  const { type, payload } = action;

  switch (type) {
    case OFFERS_UPDATED:
      return {
        ...state,
        offers: payload,
      };
    case ADVERTISEMENTS_UPDATED:
      return {
        ...state,
        advertisements: payload,
      };
    default:
      return state;
  }
}
