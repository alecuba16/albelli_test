import React, { useState, useEffect } from 'react'
import { useTable } from 'react-table'
import { connect } from "react-redux";
import BTable from 'react-bootstrap/Table';
import DatePicker from "react-datepicker";
import "react-datepicker/dist/react-datepicker.css";
import { ListGroup, Container, Row, Col, Dropdown } from 'react-bootstrap';
import { GrAddCircle } from 'react-icons/gr';

const Cell = ({
    value: initialValue,
    row: { index },
    column: { id },
    updateDataCllbk,
    removeChildItemCllbk,
    generalDisable
}) => {
    // We need to keep and update the state of the cell normally
    const [value, setValue] = useState(initialValue)

    const date = (new Date(value));
    const ExampleCustomTimeInput = ({ date, value, onChange }) => (
        <input
            value={value}
            onChange={(e) => onChange(e.target.value)}
            style={{ border: "solid 1px pink" }}
        />
    );

    const onChangeNumber = e => {
        setValue(parseInt(e.target.value));
    }

    const onChange = e => {
        setValue(e.target.value)
    }

    const onChangeDate = e => {
        setValue(e)
        onBlur()
    }

    // We'll only update the external data when the input is blurred
    const onBlur = () => {
        updateDataCllbk(index, id, value)
    }

    // If the initialValue is changed external, sync it up with our state
    useEffect(() => {
        setValue(initialValue)
    }, [initialValue])
    switch (id) {
        case 'id':
            return <input value={value} disabled />
        case 'start_date':
        case 'end_date':
            return <DatePicker id={id} selected={date} showTimeInput customTimeInput={<ExampleCustomTimeInput />} onChange={onChangeDate} disabled={generalDisable} />;
        case 'discount_value':
            return <input type="number" value={value} onChange={onChangeNumber} onBlur={onBlur} disabled={generalDisable} />;
        case 'offers':
        case 'advertisements':
            return (value != null &&
                <Container>
                    <Row>
                        <Col>
                            <ListGroup>
                                {value.map((item, i) => {
                                    return <ListGroup.Item key={i} action onClick={() => removeChildItemCllbk(index, item)} disabled={generalDisable}>{id === "offers" ? item.product_name : item.title}</ListGroup.Item>
                                })
                                }</ListGroup>
                        </Col>
                    </Row>
                </Container>
            );
        //return <input type="number" value={value} onChange={onChangeNumber} onBlur={onBlur} disabled={generalDisable} />;

        default:
            return <input value={value} onChange={onChange} onBlur={onBlur} disabled={generalDisable} />;
    }
}


// Set our editable cell renderer as the default Cell renderer
const defaultColumn = {
    Cell: Cell
}

function possibleValues(currentRelated, childList) {
    if (childList == null || childList.length === 0)
        return [];
    if (currentRelated == null || currentRelated.length === 0) {
        return childList;
    }
    const availableRelated = childList.filter((rl) => !currentRelated.some((cr) => cr.id === rl.id));
    return availableRelated;
}

function Table({ columns, data, updateDataCllbk, generalDisable, removeChildItemCllbk, childList, childKey, childDescKey, childAddCllbk }) {
    // For this example, we're using pagination to illustrate how to stop
    // the current page from resetting when our data changes
    // Otherwise, nothing is different here.
    const {
        getTableProps,
        getTableBodyProps,
        headerGroups,
        prepareRow,
        rows
    } = useTable(
        {
            columns,
            data,
            defaultColumn,
            updateDataCllbk,
            removeChildItemCllbk,
            childAddCllbk,
            generalDisable
        }
    )


    // Render the UI for your table
    return (
        <>
            <BTable striped bordered hover size="sm"  {...getTableProps()}>
                <thead>
                    {headerGroups.map(headerGroup => (
                        <tr {...headerGroup.getHeaderGroupProps()}>
                            {headerGroup.headers.map(column => (
                                <th {...column.getHeaderProps()}>{column.render('Header')}</th>
                            ))}
                            <th>Add related</th>
                        </tr>
                    ))}
                </thead>
                <tbody {...getTableBodyProps()}>
                    {rows.map(row => {
                        prepareRow(row)
                        return (
                            <tr {...row.getRowProps()}>
                                {row.cells.map(cell => {
                                    return (
                                        <td {...cell.getCellProps()}>
                                            {cell.render('Cell')}
                                        </td>
                                    )
                                })}
                                <td>
                                    <Dropdown className="d-inline mx-2" onSelect={(childId) => childAddCllbk(row.original.id, childId)}>
                                        <Dropdown.Toggle id="dropdown-autoclose-true" variant="Secondary"><GrAddCircle /></Dropdown.Toggle>

                                        <Dropdown.Menu>
                                            {possibleValues(row.original[childKey], childList).map((item, i) => {
                                                return <Dropdown.Item key={i} eventKey={item.id}>{item[childDescKey]}</Dropdown.Item>
                                            })}
                                        </Dropdown.Menu>
                                    </Dropdown>
                                </td>
                            </tr>
                        )
                    })}
                </tbody>
            </BTable>
        </>
    )
}

function mapStateToProps(state) {
    const { user } = state.auth;
    const { offers } = state.data;
    return {
        user,
        offers
    };
}


export default connect(mapStateToProps)(Table);