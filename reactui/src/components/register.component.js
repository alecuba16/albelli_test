import React, { useCallback, useState } from "react";
import Form from "react-validation/build/form";
import Input from "react-validation/build/input";
import CheckButton from "react-validation/build/button";
import { isEmail } from "validator";
import { connect } from "react-redux";
import { register } from "../redux/actions/auth";
import { MIN_PASSWORD_LENGTH, MAX_PASSWORD_LENGTH, MIN_NAME_LENGTH, MAX_NAME_LENGTH } from "./../configs";

const required = (value) => {
  if (!value) {
    return (
      <div className="alert alert-danger" role="alert">
        Required field
      </div>
    );
  }
};

const vemail = (value) => {
  if (!isEmail(value)) {
    return (
      <div className="alert alert-danger" role="alert">
        Not a valid email
      </div>
    );
  }
};

const vname = (value) => {
  if (value.length < MIN_NAME_LENGTH || value.length > MAX_NAME_LENGTH) {
    return (
      <div className="alert alert-danger" role="alert">
        The name must be between {MIN_NAME_LENGTH} and {MAX_NAME_LENGTH} characters.
      </div>
    );
  }
};

const vpassword = (value) => {
  if (value.length < MIN_PASSWORD_LENGTH || value.length > MAX_PASSWORD_LENGTH) {
    return (
      <div className="alert alert-danger" role="alert">
        The password must be between {MIN_PASSWORD_LENGTH} and {MAX_PASSWORD_LENGTH} characters.
      </div>
    );
  }
};

function Register(props) {
  const { message, dispatch, history } = props;
  const [name, setName] = useState("");
  const [email, setEmail] = useState("");
  const [password, setPassword] = useState("");
  const [successful, setSuccessful] = useState(false);
  const [loading, setLoading] = useState(false);
  const [form, setForm] = useState(null);
  const [checkBtn, setCheckBtn] = useState(null);

  const onChangeName = useCallback((e) => {
    setName(e.target.value);
  }, []);

  const onChangeEmail = useCallback((e) => {
    setEmail(e.target.value);
  }, []);

  const onChangePassword = useCallback((e) => {
    setPassword(e.target.value);
  }, []);

  const handleRegister = useCallback((e) => {
    e.preventDefault();
    setSuccessful(false);
    setLoading(true);
    form.validateAll();

    if (checkBtn.context._errors.length === 0) {
      dispatch(
        register(name, email, password)
      )
        .then(() => {
          setSuccessful(true);
          setLoading(false);
          history.push("/profile");
          window.location.reload();
        })
        .catch(() => {
          setSuccessful(false);
          setLoading(false);
        });
    }
  }, [checkBtn, dispatch, email, form, name, password, history]);
  return (
    <div className="col-md-12">
      <div className="card card-container">
        <img
          src="//ssl.gstatic.com/accounts/ui/avatar_2x.png"
          alt="profile-img"
          className="profile-img-card"
        />

        <Form
          onSubmit={handleRegister}
          ref={(c) => {
            setForm(c);
          }}
        >
          {!successful && (
            <div>
              <div className="form-group">
                <label htmlFor="name">Name</label>
                <Input
                  type="text"
                  className="form-control"
                  name="name"
                  value={name}
                  onChange={onChangeName}
                  validations={[required, vname]}
                />
              </div>

              <div className="form-group">
                <label htmlFor="email">Email</label>
                <Input
                  type="text"
                  className="form-control"
                  name="email"
                  value={email}
                  onChange={onChangeEmail}
                  validations={[required, vemail]}
                />
              </div>

              <div className="form-group">
                <label htmlFor="password">Password</label>
                <Input
                  type="password"
                  className="form-control"
                  name="password"
                  value={password}
                  onChange={onChangePassword}
                  validations={[required, vpassword]}
                />
              </div>

              <div className="form-group">
                <button
                  className="btn btn-primary btn-block"
                  disabled={loading}
                >
                  {loading && (
                    <span className="spinner-border spinner-border-sm"></span>
                  )}
                  <span>Register</span>
                </button>
              </div>
            </div>
          )}

          {message && (
            <div className="form-group">
              <div className={successful ? "alert alert-success" : "alert alert-danger"} role="alert">
                {message}
              </div>
            </div>
          )}
          <CheckButton
            style={{ display: "none" }}
            ref={(c) => {
              setCheckBtn(c);
            }}
          />
        </Form>
      </div>
    </div>
  );
}

function mapStateToProps(state) {
  const { message } = state.message;
  return {
    message,
  };
}

export default connect(mapStateToProps)(Register);
