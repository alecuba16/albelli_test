import React, { useState, useEffect, useMemo, useCallback } from "react";
import { Redirect } from "react-router-dom";
import { connect } from "react-redux";
import { addOffer, updateOffer, getOffers, deleteOfferById, deleteOneRelatedById } from "../redux/actions/offers";
import { getAdvertisements } from "../redux/actions/advertisements";
import Table from "./table.component";
import DatePicker from "react-datepicker";
import { Form, Button, Modal, Placeholder } from "react-bootstrap";

function Offers(props) {
  const { user, dispatch, offers, advertisements } = props;
  const [saving, setSaving] = useState(false);
  const defaultStartDate = new Date();
  const defaultEndDate = new Date(defaultStartDate);
  defaultEndDate.setDate(defaultEndDate.getDate() + 1);
  const defaultNew = { start_date: defaultStartDate, end_date: defaultEndDate, product_name: "New product", discount_value: 10 };
  const [newElement, setNewElement] = useState(defaultNew);
  const [enableNew, setEnableNew] = useState(false);
  const [elementData, setElementData] = useState(offers);
  const [loading, setLoading] = useState(elementData == null || elementData.length === 0);
  const [toSave, setToSave] = useState([]);
  const [childToRemove, setChildToRemove] = useState(null);
  const [childList, setChildList] = useState(null);

  const childAddCllbk = useCallback(
    (parentId, childId) => {
      setSaving(true);
      const currentParent = elementData.find((d) => d.id === parentId);
      const newChild = advertisements.find((d) => d.id === parseInt(childId));
      if (!currentParent.hasOwnProperty("advertisements") || currentParent.advertisements == null) currentParent.advertisements = [];
      if (newChild == null) return;
      currentParent.advertisements.push(newChild);
      dispatch(updateOffer(currentParent))
        .then((response) => {
          setSaving(false);
        })
        .catch((error) => {
          setSaving(false);
        });
    },
    [advertisements, dispatch, elementData]
  );

  const onChangeNew = useCallback(
    (element, key) => {
      setNewElement({ ...newElement, [key]: element });
    },
    [newElement]
  );

  const ExampleCustomTimeInput = useMemo((date, value, onChange) => {
    return <input value={value} onChange={(e) => onChange(e.target.value)} style={{ border: "solid 1px pink" }} />;
  }, []);

  const submitNew = useCallback(
    (event) => {
      setSaving(true);
      event.preventDefault();
      event.stopPropagation();
      dispatch(addOffer(newElement))
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
    setElementData(offers);
  }, [offers]);

  const saveData = useCallback(() => {
    setSaving(true);
    toSave.forEach((id) => {
      dispatch(updateOffer(elementData.find((d) => d.id === id)))
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
        const newElement = old.map((row, index) => {
          if (index === rowIndex) {
            setToSave([...toSave, old[rowIndex].id]);
            return {
              ...old[rowIndex],
              [columnId]: value,
            };
          }
          return row;
        });
        return newElement;
      });
    },
    [toSave]
  );

  const removeChildItem = useCallback(
    (indexParent, childObj) => {
      setChildToRemove({ product_name: elementData[indexParent].product_name, id: elementData[indexParent].id, child_id: childObj.id, child_desc: childObj.title });
    },
    [elementData]
  );

  const removeChildConfirmation = useCallback(
    (state) => {
      if (state) {
        //Call service for removal
        dispatch(deleteOneRelatedById(childToRemove.id, childToRemove.child_id))
          .then((response) => {
            setSaving(false);
            //const processedOffers = returnAdvertisements(response);
            setElementData(response);
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
      dispatch(deleteOfferById(parseInt(row.original.id)))
        .then((response) => {
          setSaving(false);
          dispatch(getOffers());
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
        Header: "Product name",
        accessor: "product_name",
      },
      {
        Header: "Discount value",
        accessor: "discount_value",
      },
      {
        Header: "Start date",
        accessor: "start_date",
      },
      {
        Header: "End date",
        accessor: "end_date",
      },
      {
        Header: "Related Advertisements",
        accessor: "advertisements",
      },
    ],
    [deleteOneElement]
  );

  useEffect(() => {
    if (offers == null) {
      dispatch(getOffers())
        .then((response) => {
          if (advertisements != null) setLoading(false);
        })
        .catch((error) => {
          setLoading(false);
        });
    } else {
      setElementData(offers);
    }

    if (advertisements == null) {
      dispatch(getAdvertisements())
        .then((response) => {
          if (offers != null) setLoading(false);
          setChildList(response);
        })
        .catch((error) => {
          setLoading(false);
        });
    } else {
      setChildList(advertisements);
    }
  }, [offers, advertisements, dispatch]);

  if (!user) {
    return <Redirect to="/login" />;
  }

  return (
    <div className="container">
      <header className="jumbotron">
        <h3>Offers</h3>
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

        {!loading && (elementData === null || (elementData != null && elementData.length === 0)) && <h3>There are no offers</h3>}
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
              <Form.Group className="mb-3" controlId="formProductName">
                <Form.Label>Product name</Form.Label>
                <Form.Control type="text" placeholder={newElement.product_name} onChange={(e) => onChangeNew(e.target.value, "product_name")} />
              </Form.Group>
              <Form.Group className="mb-3" controlId="formDiscountValue">
                <Form.Label>Discount value</Form.Label>
                <Form.Control type="number" placeholder={newElement.discount_value} onChange={(e) => onChangeNew(e.target.value, "discount_value")} />
              </Form.Group>
              <Form.Group className="mb-3" controlId="formStartDate">
                <Form.Label>Start date</Form.Label>
                <DatePicker
                  id="start_date"
                  selected={newElement.start_date}
                  showTimeInput
                  customTimeInput={<ExampleCustomTimeInput />}
                  onChange={(e) => onChangeNew(e, "start_date")}
                />
              </Form.Group>
              <Form.Group className="mb-3" controlId="formEndDate">
                <Form.Label>End date</Form.Label>
                <DatePicker id="end_date" selected={newElement.end_date} showTimeInput customTimeInput={<ExampleCustomTimeInput />} onChange={(e) => onChangeNew(e, "end_date")} />
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
              generalDisable={saving === true}
              updateDataCllbk={updateDataCllbk}
              removeChildItemCllbk={removeChildItem}
              childList={childList}
              childKey="advertisements"
              childDescKey="title"
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
                  Remove <b>{childToRemove.child_desc}</b> from the advertisement <b>{childToRemove.product_name}?</b>
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
  const { offers, advertisements } = state.data;
  return {
    user,
    offers,
    advertisements,
  };
}

export default connect(mapStateToProps)(Offers);
