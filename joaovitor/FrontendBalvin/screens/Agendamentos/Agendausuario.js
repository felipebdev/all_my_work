import { StyleSheet, Text, View, TouchableOpacity, Image, Button, Modal } from 'react-native'
import React, { useEffect, useState, useRef } from 'react'
import { MaterialIcons } from '@expo/vector-icons';
import DatePicker from 'react-native-modern-datepicker';
import { getToday, getFormatedDate} from 'react-native-modern-datepicker';
import { StatusBar } from 'expo-status-bar';
import AsyncStorage from '@react-native-async-storage/async-storage';


export default function Agendausuario({ navigation, route }) {

    const { fuseremail, fuserid } = route.params;

    const [ouruserdata, setOuruserdata] = React.useState(null);
    const [fuserdata, setFuserdata] = React.useState(null);

    const [userid, setUserid] = React.useState(null);
  



    useEffect(() => {
        loaddata()
    }, [])

    const loaddata = async () => {
        AsyncStorage.getItem('user')
            .then(async (value) => {
                fetch('http://192.168.0.54:3000/userdata', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': 'Bearer ' + JSON.parse(value).token
                    },
                    body: JSON.stringify({ email: JSON.parse(value).user.email })
                })
                    .then(res => res.json()).then(data => {
                        if (data.message == 'Usuário Encontrado') {
                            // console.log('our user data ', data.user.username)
                            setOuruserdata(data.user)
                            setUserid(data.user._id)

                            fetch('http://192.168.0.54:3000/outrosusuarios', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                },
                                body: JSON.stringify({ email: fuseremail })
                            })
                                .then(res => res.json())
                                .then(async data1 => {
                                    if (data1.message == 'Usuário Encontrado') {
                                        // console.log('fuser data ', data1.user.username)
                                        setFuserdata(data1.user)
                                       
                                    }
                                    else {
                                        alert('Usuário não encontrado')
                                        navigation.navigate('Pesquisar')
                                        // navigation.navigate('Login')
                                    }
                                })
                                .catch(err => {
                                    // console.log(err)
                                   
                                    navigation.navigate('Pesquisar')
                                })
                        }
                        else {
                           
                            navigation.navigate('Login')
                        }
                    })
                    .catch(err => {
                        navigation.navigate('Login')
                    })
            })
            .catch(err => {
                navigation.navigate('Login')
            })
    }

    const scrollViewRef = useRef();

    const today = new Date();
    const startDate = getFormatedDate(today.setDate(today.getDate()), 'YYYY/MM/DD')

    const [open, setOpen] = useState(false)
    const [date, setDate] = useState('27/03/2023')

    function handleOnPress () {
        setOpen(!open);
    }

    function handleChange (propDate) {
        setDate(propDate)
    }

  return (
    <View style={{width: '100%', height: '100%', backgroundColor: 'white'}}>
        <StatusBar hidden/>
         
                <View style={{alignItems: 'center'}}>

                <TouchableOpacity onPress={() => navigation.goBack()} style={{marginStart: -330, marginTop: 20}} >
                    <MaterialIcons name="arrow-back-ios" size={27} color="black" />
                </TouchableOpacity>


                {
                    fuserdata?.profilepic ?
                    
                        <TouchableOpacity style={{width: 110, height: 110, backgroundColor: '#ec230d',
                        borderRadius: 75, marginTop: 20, justifyContent: 'center', alignItems: 'center'}}>
                        <Image source={{ uri: fuserdata?.profilepic }} style={styles.profilepic} />
                        </TouchableOpacity>
                        :
                        <TouchableOpacity style={{width: 110, height: 110, backgroundColor: '#ec230d',
                        borderRadius: 75, marginTop: 20, justifyContent: 'center', alignItems: 'center'}}>
                        <Image source={('./../../images/nopic.png')} style={styles.profilepic} />
                        </TouchableOpacity>

                }
                                 

                                  <Text style={{marginTop: 10, fontSize: 20, fontWeight: 'bold'}}>{fuserdata?.nome}</Text>
                                  <Text>Nome do Cargo</Text>

                                  </View>

                                  <View style={{width: '100%', borderTopEndRadius: 30, borderTopStartRadius: 30, elevation: 10,
                                backgroundColor: '#FAFAFA', height: 550, marginTop: 30}}>
                                    
                                    <Text  style={{fontSize: 25, marginTop: 20, marginStart: 40, fontWeight: 'bold'}}>Data</Text>

                                    <TouchableOpacity style={{ width: 350, height: 50, marginTop: 10, alignSelf: 'center', backgroundColor: 'white'
                                ,justifyContent:"center", borderRadius: 10, borderColor: 'gray', borderWidth: 0.5}} onPress={handleOnPress}>
                                        <Text style={{fontWeight: 'bold', fontSize: 13, marginStart: 20, color: 'gray'}}>Abrir calendario</Text>
                                    </TouchableOpacity>
                                    
                                    <Modal
                                    animationType='slide'
                                    transparent={true}
                                    visible={open}>

                                      <View>
                                        
                                      <DatePicker mode='calendar' selected={date} minimumDate={startDate} onDateChange={handleChange} /> 

                                        <TouchableOpacity onPress={handleOnPress}>
                                            <Text>Fechar</Text>
                                        </TouchableOpacity>

                                        </View>  

                                    </Modal>

                                    <View style={{flexDirection: 'row', marginTop: 30}}>
                                        <Text style={{marginStart: 27, fontStyle: 'italic'}}><View style={{width: 10,
                                        height: 10, borderRadius: 150, backgroundColor: 'green'}}></View> Disponivel</Text>
                                        <Text style={{marginStart: 40, fontStyle: 'italic'}}><View style={{width: 10,
                                        height: 10, borderRadius: 150, backgroundColor: '#ec230d'}}></View> Selecionado</Text>
                                        <Text style={{marginStart: 40, fontStyle: 'italic'}}><View style={{width: 10,
                                        height: 10, borderRadius: 150, backgroundColor: 'gray'}}></View> Indisponivel</Text>
                                    </View>

                                    <View style={{flexDirection: 'row', marginTop: 30, }}>
                                        <TouchableOpacity style={{marginStart: 20,width: 100, height:30, backgroundColor: 'white', alignItems: 'center',
                                         justifyContent: 'center', borderRadius: 10}}>
                                            <Text>7:00 AM</Text>
                                        </TouchableOpacity>

                                        <TouchableOpacity style={{marginStart: 20,width: 100, height:30, backgroundColor: 'white', alignItems: 'center',
                                         justifyContent: 'center', borderRadius: 10}}>
                                            <Text>8:00 AM</Text>
                                        </TouchableOpacity>
                                        
                                        <TouchableOpacity style={{marginStart: 20,width: 100, height:30, backgroundColor: 'white', alignItems: 'center',
                                         justifyContent: 'center', borderRadius: 10}}>
                                            <Text>9:00 AM</Text>
                                        </TouchableOpacity>
                                    </View>

                                    <View style={{flexDirection: 'row', marginTop: 20, }}>
                                        <TouchableOpacity style={{marginStart: 20,width: 100, height:30, backgroundColor: 'white', alignItems: 'center',
                                         justifyContent: 'center', borderRadius: 10}}>
                                            <Text>10:00 AM</Text>
                                        </TouchableOpacity>

                                        <TouchableOpacity style={{marginStart: 20,width: 100, height:30, backgroundColor: 'white', alignItems: 'center',
                                         justifyContent: 'center', borderRadius: 10}}>
                                            <Text>11:00 AM</Text>
                                        </TouchableOpacity>
                                        
                                        <TouchableOpacity style={{marginStart: 20,width: 100, height:30, backgroundColor: 'white', alignItems: 'center',
                                         justifyContent: 'center', borderRadius: 10}}>
                                            <Text>12:00 AM</Text>
                                        </TouchableOpacity>
                                    </View>

                                    <View style={{flexDirection: 'row', marginTop: 20, }}>
                                        <TouchableOpacity style={{marginStart: 20,width: 100, height:30, backgroundColor: 'white', alignItems: 'center',
                                         justifyContent: 'center', borderRadius: 10}}>
                                            <Text>13:00 AM</Text>
                                        </TouchableOpacity>

                                        <TouchableOpacity style={{marginStart: 20,width: 100, height:30, backgroundColor: 'white', alignItems: 'center',
                                         justifyContent: 'center', borderRadius: 10}}>
                                            <Text>14:00 AM</Text>
                                        </TouchableOpacity>
                                        
                                        <TouchableOpacity style={{marginStart: 20,width: 100, height:30, backgroundColor: 'white', alignItems: 'center',
                                         justifyContent: 'center', borderRadius: 10}}>
                                            <Text>15:00 AM</Text>
                                        </TouchableOpacity>
                                    </View>

                                    <View style={{flexDirection: 'row', marginTop: 20, }}>
                                        <TouchableOpacity style={{marginStart: 20,width: 100, height:30, backgroundColor: 'white', alignItems: 'center',
                                         justifyContent: 'center', borderRadius: 10}}>
                                            <Text>16:00 AM</Text>
                                        </TouchableOpacity>

                                        <TouchableOpacity style={{marginStart: 20,width: 100, height:30, backgroundColor: 'white', alignItems: 'center',
                                         justifyContent: 'center', borderRadius: 10}}>
                                            <Text>17:00 AM</Text>
                                        </TouchableOpacity>
                                        
                                        <TouchableOpacity style={{marginStart: 20,width: 100, height:30, backgroundColor: 'white', alignItems: 'center',
                                         justifyContent: 'center', borderRadius: 10}}>
                                            <Text>18:00 AM</Text>
                                        </TouchableOpacity>
                                    </View>

                                    <TouchableOpacity style={{width: '90%', alignSelf: 'center', backgroundColor: '#ec230d', marginTop: 50, height: 40,
                                alignItems: 'center', justifyContent: 'center', borderRadius: 5}}>
                                    <Text style={{color: 'white', fontSize: 15, fontWeight: 'bold'}}>Confirmar horário</Text>
                                  </TouchableOpacity>

                                  </View>
          
                                  
    </View>
  )
}


const styles = StyleSheet.create({
    profilepic: {
        width: 100,
        height: 100,
        backgroundColor: 'white',
        borderRadius: 75,
        margin: 10,
    },
})