<template>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="content">
                    <h1 style="text-align: center">Please choose CSV file</h1>
                    <p style="text-align: center;">
                        <input
                                class="file"
                                type="file"
                                @change="onSelectFile($event)"
                        >
                    </p>
                    <p class="foundation" v-if="showMap">
                        <b>
                            Map your fields to Autopilot's fields:
                        </b>
                        <span v-if="errorMessage" class="errorMessage" v-text="errorMessage"></span>
                    </p>

                    <table class="table table-bordered custom_border" v-if="showMap">
                        <tr>
                            <th class="first_th">Spreadsheet Field</th>
                            <th class="second_th">Autopilot Fields</th>
                        </tr>
                        <tr>
                            <td>team_id</td>
                            <td>
                                <select class="form-control" v-model="team_id">
                                    <option v-for="(index,item) in values" :value="item" :key="item">{{index}}</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td>unsubscribed_status</td>
                            <td>
                                <select class="form-control" v-model="unsubscribed_status">
                                    <option v-for="(index,item) in values" :value="item" :key="item">{{index}}</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td>first_name</td>
                            <td>
                                <select v-model="first_name" class="form-control">
                                    <option v-for="(index,item) in values" :value="item" :key="item">{{index}}</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td>last_name</td>
                            <td>
                                <select v-model="last_name" class="form-control">
                                    <option v-for="(index,item) in values" :value="item">{{index}}</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td>phone</td>
                            <td>
                                <select v-model="phone" class="form-control">
                                    <option v-for="(index,item) in values" :value="item">{{index}}</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td>email</td>
                            <td>
                                <select v-model="email" class="form-control">
                                    <option v-for="(index,item) in values" :value="item">{{index}}</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td>sticky_phone_number_id</td>
                            <td>
                                <select v-model="sticky_phone_number_id" class="form-control">
                                    <option v-for="(index,item) in values" :value="item">{{index}}</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td>twitter_id</td>
                            <td>
                                <select v-model="twitter_id" class="form-control">
                                    <option v-for="(index,item) in values" :value="item">{{index}}</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td>fb_messenger_id</td>
                            <td>
                                <select v-model="fb_messenger_id" class="form-control">
                                    <option v-for="(index,item) in values" :value="item">{{index}}</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td>time_zone</td>
                            <td>
                                <select v-model="time_zone" class="form-control">
                                    <option v-for="(index,item) in values" :value="item">{{index}}</option>
                                </select>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        <hr v-if="showMap">
        <div class="row footer-part" v-if="showMap">
            <div class="col-md-12">
                <button class="btn">Cancel</button>
                <button class="btn btn-success" @click="submit()">Continue</button>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: 'autopilot_fields',
        data() {
            return {
                columns: {
                    "team_id": "Team ID",
                    "unsubscribed_status": "Unsubscribed Status",
                    "first_name": "First Name",
                    "last_name": "Last Name",
                    "phone": "Phone",
                    "email": "Email",
                    "sticky_phone_number_id": "Sticky Phone Number ID",
                    "twitter_id": "Twitter Id",
                    "fb_messenger_id": "Fb Messenger ID",
                    "time_zone": "Time Zone"
                },
                values: {},
                team_id: "team_id",
                unsubscribed_status: "unsubscribed_status",
                phone: "phone",
                sticky_phone_number_id: "sticky_phone_number_id",
                twitter_id: "twitter_id",
                fb_messenger_id: "fb_messenger_id",
                time_zone: "time_zone",
                first_name: "first_name",
                last_name: "last_name",
                email: "email",
                file: null,
                content: null,
                first_line: "",
                objectCreated: false,
                finalValues: null,
                errorMessage: ""
            }
        },
        computed: {
            showMap() {
                return (!this.file && !this.objectCreated) ? false : true;
            }
        },
        methods: {
            submit() {
                let obj = {};
                obj.first_name = this.first_name;
                obj.last_name = this.last_name;
                obj.email = this.email;
                obj.phone = this.phone;
                obj.file = this.file;
                obj.team_id = this.team_id;
                obj.unsubscribed_status = this.unsubscribed_status;
                obj.sticky_phone_number_id = this.sticky_phone_number_id;
                obj.twitter_id = this.twitter_id;
                obj.fb_messenger_id = this.fb_messenger_id;
                obj.time_zone = this.time_zone;
                obj._token = document.querySelector('meta[name="csrf-token"]').getAttribute('value');

                let formData = new FormData();

                for (let index   in  obj) {
                    if (index == null)
                        formData.append(index, "");
                    else
                        formData.append(index, obj[index]);
                }

                axios.post(`http://localhost:8000/create`, formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data',
                    }
                })
                    .then((res) => {
                        console.log(res);
                    })
                    .catch((err) => {
                        if(err.response.data.errorInfo){
                            this.errorMessage = err.response.data.errorInfo
                        }else{
                            this.errorMessage = err.response.data.error
                        }
                    })
            },
            onSelectFile(event) {
                let _this = this;
                let files = event.target.files;
                let file = files[0];
                if (files && file) {
                    let reader = new FileReader();
                    reader.onload = this.handleReaderLoaded.bind(this);
                    reader.readAsBinaryString(file);
                    this.file = file;
                    reader.onloadend = function (evt) {
                        let lines = evt.target.result.split("\r");
                        this.first_line = lines[0];
                        let first_line_values = this.first_line.split(',');
                        first_line_values.map(item => {
                            _this.values[item] = item;
                        })
                        _this.values = Object.assign({}, _this.values)
                    };
                    this.objectCreated = true;

                }
            },
            handleReaderLoaded(readerEvt) {                                   //img to binary string
                let binaryString = readerEvt.target.result;
                this.url = 'data:image/gif;base64,' + btoa(binaryString);
            },
        },
    }
</script>

<style>
    * {
        margin: 0;
        padding: 0;

    }

    .table {
        background-color: #F2F6F8;
    }

    .footer-part {
        text-align: right;
    }

    .foundation {
        margin-top: 12px;
        padding-left: 21px;
        font-weight: bold;
    }

    .file {
        padding-left: 19px;
        margin-bottom: 16px;
    }

    .custom_border {
        margin-left: 20px;
        margin-top: -11px;
    }
    .errorMessage{
        color: red;
    }
</style>
