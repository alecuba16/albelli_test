import React, { useCallback, useEffect, useState } from "react";
import { connect } from "react-redux";
import { Router, Switch, Route, Link } from "react-router-dom";

import "bootstrap/dist/css/bootstrap.min.css";
import "./App.css";

import Login from "./components/login.component";
import Register from "./components/register.component";
import Profile from "./components/profile.component";

import { logout, checkLoginStatus } from "./redux/actions/auth";
import { clearMessage } from "./redux/actions/message";

import { history } from './helpers/history';
import Offers from "./components/offer.component";
import Advertisement from "./components/advertisement.component";

function App(props) {
  const { user, dispatch } = props;
  const [showOffers, setShowOffers] = useState(false);
  const [showAdvertisements, setShowAdvertisements] = useState(false);
  history.listen((location) => {
    props.dispatch(clearMessage()); // clear message when changing location
  });

  useEffect(() => {
    dispatch(checkLoginStatus());
    setShowOffers(user != null);
    setShowAdvertisements(user != null);
  }, [user, dispatch]);

  const handleLogout = useCallback(() => {
    dispatch(logout());
  }, [dispatch]);

  return (
    <Router history={history}>
      <div>
        <nav className="navbar navbar-expand navbar-dark bg-dark">
          <div className="navbar-nav mr-auto">
            <li className="nav-item">
              <Link to={"/profile"} className="nav-link">
                Profile
              </Link>
            </li>

            {showOffers && (
              <li className="nav-item">
                <Link to={"/offers"} className="nav-link">
                  Offers
                </Link>
              </li>
            )}

            {showAdvertisements && (
              <li className="nav-item">
                <Link to={"/advertisements"} className="nav-link">
                  Advertisements
                </Link>
              </li>
            )}
          </div>

          {user ? (
            <div className="navbar-nav ml-auto">
              <li className="nav-item">
                <Link to={"/profile"} className="nav-link">
                  {user.username}
                </Link>
              </li>
              <li className="nav-item">
                <a href="/login" className="nav-link" onClick={handleLogout}>
                  LogOut
                </a>
              </li>
            </div>
          ) : (
            <div className="navbar-nav ml-auto">
              <li className="nav-item">
                <Link to={"/login"} className="nav-link">
                  Login
                </Link>
              </li>

              <li className="nav-item">
                <Link to={"/register"} className="nav-link">
                  Sign Up
                </Link>
              </li>
            </div>
          )}
        </nav>

        <div className="container mt-3">
          <Switch>
            <Route exact path={["/", "/profile"]} component={Profile} />
            <Route exact path="/login" component={Login} />
            <Route exact path="/register" component={Register} />
            <Route exact path="/offers" component={Offers} />
            <Route exact path="/advertisements" component={Advertisement} />
          </Switch>
        </div>
      </div>
    </Router>
  );
}

function mapStateToProps(state) {
  const { user } = state.auth;
  return {
    user,
  };
}

export default connect(mapStateToProps)(App);
