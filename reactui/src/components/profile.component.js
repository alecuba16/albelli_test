import React from "react";
import { Redirect } from 'react-router-dom';
import { connect } from "react-redux";

function Profile(props) {
  const { user } = props;

  if (!user) {
    return <Redirect to="/login" />;
  }

  return (
    <div className="container">
      <header className="jumbotron">
        <h3>
          Username: <strong>{user.name}</strong>
        </h3>
      </header>
      <p>
        <strong>Token:</strong> {user.token}
      </p>
    </div>
  );
}

function mapStateToProps(state) {
  const { user } = state.auth;
  return {
    user,
  };
}

export default connect(mapStateToProps)(Profile);
