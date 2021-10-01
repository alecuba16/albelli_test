import React, { useState, useEffect, useMemo, useCallback } from "react";
import { Redirect } from "react-router-dom";
import { connect } from "react-redux";
import Table from "./table.component";
import { Form, Button, Modal, Placeholder } from "react-bootstrap";
import { getAdvertisements, deleteAdvertisementById, addAdvertisement, updateAdvertisement, deleteOneRelatedById } from "../redux/actions/advertisements";
import { getOffers } from "../redux/actions/offers";

function Advertisements(props) {
  const { user, dispatch, advertisements, offers } = props;
  const [saving, setSaving] = useState(false);
  const defaultNew = { title: "New advertisement" };
  const [newElement, setNewElement] = useState(defaultNew);
  const [enableNew, setEnableNew] = useState(false);
  const [elementData, setElementData] = useState(advertisements);
  const [loading, setLoading] = useState(elementData == null || elementData.length === 0);
  const [toSave, setToSave] = React.useState([]);
  const [childToRemove, setChildToRemove] = useState(null);
  const [childList, setChildList] = React.useState(null);

  const childAddCllbk = useCallback(
    (parentId, childId) => {
      setSaving(true);
      const currentParent = elementData.find((d) => d.id === parentId);
      const newChild = offers.find((d) => d.id === parseInt(childId));
      if (!currentParent.hasOwnProperty("offers") || currentParent.offers == null) currentParent.offers = [];
      if (newChild == null) return;
      currentParent.offers.push(newChild);
      dispatch(updateAdvertisement(currentParent))
        .then((response) => {
          setSaving(false);
        })
        .catch((error) => {
          setSaving(false);
        });
    },
    [offers, dispatch, elementData]
  );

  const onChangeNew = useCallback(
    (element, key) => {
      setNewElement({ ...newElement, [key]: element });
    },
    [newElement]
  );

  const submitNew = useCallback(
    (event) => {
      setSaving(true);
      event.preventDefault();
      event.stopPropagation();
      dispatch(addAdvertisement(newElement))
        .then((response) => {
          setSaving(false);
        })
        .catch((error) => {
          setSaving(false);
        });
      setNewElement(defaultNew);
      setEnableNew(false);
    },
    [defaultNew, dispatch, newElement]
  );

  const resetData = useCallback(() => {
    setToSave([]);
    setElementData(advertisements);
  }, [advertisements]);

  const saveData = useCallback(() => {
    setSaving(true);
    toSave.forEach((id) => {
      dispatch(updateAdvertisement(elementData.find((d) => d.id === id)))
        .then((response) => {
          setSaving(false);
        })
        .catch((error) => {
          setSaving(false);
        });
    });
    setToSave([]);
  }, [dispatch, toSave, elementData]);

  const updateDataCllbk = useCallback(
    (rowIndex, columnId, value) => {
      setElementData((old) => {
        const newData = old.map((row, index) => {
          if (index === rowIndex) {
            setToSave([...toSave, old[rowIndex].id]);
            return {
              ...old[rowIndex],
              [columnId]: value,
            };
          }
          return row;
        });
        return newData;
      });
    },
    [toSave]
  );

  const removeChildItem = useCallback(
    (indexParent, childObj) => {
      setChildToRemove({ title: elementData[indexParent].title, id: elementData[indexParent].id, child_id: childObj.id, child_desc: childObj.product_name });
    },
    [elementData]
  );

  const removeChildConfirmation = useCallback(
    (state) => {
      if (state) {
        setSaving(true);
        dispatch(deleteOneRelatedById(childToRemove.id, childToRemove.child_id))
          .then((response) => {
            setSaving(false);
          })
          .catch((error) => {
            setSaving(false);
          });
      }
      setChildToRemove(null);
    },
    [childToRemove, dispatch]
  );

  const deleteOneElement = useCallback(
    (row) => {
      setSaving(true);
      dispatch(deleteAdvertisementById(parseInt(row.original.id)))
        .then((response) => {
          setSaving(false);
          dispatch(getAdvertisements());
        })
        .catch((error) => {
          setSaving(false);
        });
    },
    [setSaving, dispatch]
  );

  const columns = useMemo(
    () => [
      {
        Header: "Delete",
        Cell: ({ row }) => (
          <div>
            <Button variant="danger" onClick={() => deleteOneElement(row)} size="sm">
              Delete
            </Button>
          </div>
        ),
      },
      {
        Header: "Id",
        accessor: "id",
      },
      {
        Header: "Advertisement title",
        accessor: "title",
      },
      {
        Header: "Related offers",
        accessor: "offers",
      },
    ],
    [deleteOneElement]
  );

  useEffect(() => {
    if (advertisements == null) {
      dispatch(getAdvertisements())
        .then((response) => {
          if (offers != null) setLoading(false);
        })
        .catch((error) => {
          setLoading(false);
        });
    } else {
      setElementData(advertisements);
    }
    if (offers === null) {
      dispatch(getOffers())
        .then((response) => {
          if (advertisements != null) setLoading(false);
          setChildList(response);
        })
        .catch((error) => {
          setLoading(false);
        });
    } else {
      setChildList(offers);
    }
  }, [advertisements, offers, dispatch]);

  if (!user) {
    return <Redirect to="/login" />;
  }

  return (
    <div className="container">
      <header className="jumbotron">
        <h3>Advertisements</h3>
        {loading && (
          <div>
            <Placeholder xs={6} />
            <Placeholder className="w-75" /> <Placeholder style={{ width: "35%" }} />
            <div className="d-block align-items-center text-center">
              <strong style={{ paddingRight: "50px" }}>Loading...</strong>
              <div className="spinner-border ml-auto" role="status" aria-hidden="true"></div>
            </div>
          </div>
        )}

        {saving && (
          <div className="d-flex align-items-center">
            <strong>Saving data to API...</strong>
            <div className="spinner-border ml-auto" role="status" aria-hidden="true"></div>
          </div>
        )}

        {!loading && (elementData === null || (elementData != null && elementData.length === 0)) && <h3>There are no advertisements</h3>}
      </header>
      {elementData != null && elementData.length > 0 && (
        <div>
          {toSave != null && toSave.length > 0 && (
            <div>
              <Button variant="primary" onClick={saveData}>
                Save
              </Button>
              <Button variant="primary" onClick={resetData}>
                Reset
              </Button>
            </div>
          )}
          {!loading && enableNew === false && toSave != null && toSave.length === 0 && childToRemove === null && (
            <Button variant="primary" onClick={() => setEnableNew(true)} disabled={saving === true}>
              Add One
            </Button>
          )}
          {enableNew === true && (
            <Form onSubmit={submitNew}>
              <Form.Group className="mb-3" controlId="formTitle">
                <Form.Label>Advertisement title</Form.Label>
                <Form.Control type="text" placeholder={newElement.title} onChange={(e) => onChangeNew(e.target.value, "title")} />
              </Form.Group>
              <Button variant="primary" type="submit">
                Add
              </Button>
            </Form>
          )}
          {!loading && enableNew === false && childToRemove === null && (
            <Table
              columns={columns}
              data={elementData}
              generalDisable={saving}
              updateDataCllbk={updateDataCllbk}
              removeChildItemCllbk={removeChildItem}
              childList={childList}
              childKey="offers"
              childDescKey="product_name"
              childAddCllbk={childAddCllbk}
            />
          )}
          {childToRemove != null && (
            <Modal.Dialog>
              <Modal.Header closeButton>
                <Modal.Title>Remove from advertisement?</Modal.Title>
              </Modal.Header>
              <Modal.Body>
                <p>
                  Remove <b>{childToRemove.child_desc}</b> from the advertisement <b>{childToRemove.title}?</b>
                </p>
              </Modal.Body>

              <Modal.Footer>
                <Button variant="secondary" onClick={() => removeChildConfirmation(false)}>
                  No
                </Button>
                <Button variant="primary" onClick={() => removeChildConfirmation(true)}>
                  Yes
                </Button>
              </Modal.Footer>
            </Modal.Dialog>
          )}
        </div>
      )}
    </div>
  );
}
function mapStateToProps(state) {
  const { user } = state.auth;
  const { advertisements, offers } = state.data;
  return {
    user,
    advertisements,
    offers,
  };
}

export default connect(mapStateToProps)(Advertisements);
