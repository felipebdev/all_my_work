import { StyleSheet, Text, View, TouchableOpacity, Image, Button, Modal } from 'react-native'
import React, { useEffect, useState, useRef } from 'react'
import AsyncStorage from '@react-native-async-storage/async-storage';
import { AntDesign } from '@expo/vector-icons';
import { FontAwesome } from '@expo/vector-icons';
import { MaterialIcons } from '@expo/vector-icons';

const Outroususariosfotos = ({ navigation, route }) => {

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

  return (
    <View>

<View style={{flexDirection: 'row', marginTop: 0, height: 50, alignItems: 'center' }}>

        <TouchableOpacity onPress={() => navigation.goBack()} style={{marginTop: 0,
        marginStart: 15}}>
                <MaterialIcons name="arrow-back-ios" size={27} color="black" />
            </TouchableOpacity>

            <Text style={{marginStart: 130, marginTop: 0,
                    fontSize: 20, color: 'black', fontWeight: 'bold'}}>Fotos</Text>
            </View>

      <View style={{backgroundColor: 'white', width: '100%', height: 50,
    alignItems: 'center',  borderColor: 'gray', borderWidth: 0.5, flexDirection: 'row'}}>
             {
                    fuserdata?.profilepic ?
                    
                       <Image source={{ uri: fuserdata?.profilepic }} style={{width: 40, height: 40, borderRadius: 150,
                    marginStart: 10}}/>
                        :
                    
                        <Image source={('./../../images/nopic.png')} style={{width: 40, height: 40, borderRadius: 150,
                            marginStart: 10}}/>
                      
                }
                <Text style={{marginStart: 10, fontSize: 17, fontWeight: 'bold'}}>{fuserdata?.nome}</Text>
                
      </View>

            <View style={{width: '100%', height:350, backgroundColor: 'white', borderColor: 'gray', borderWidth: 0.5, }}>

            </View>

            <TouchableOpacity style={{marginTop: -70,marginStart: 15, width: 40, height: 40,
                            backgroundColor: 'white', alignItems: 'center', justifyContent: 'center',
                            borderRadius: 300, elevation: 10}}>
                            <AntDesign name="heart" size={15} color="black" style={{ color: '#DC143C',
        fontSize: 20}} onPress={() => {
                            
                            }} />
                            </TouchableOpacity>


    </View>
  )
}

export default Outroususariosfotos

const styles = StyleSheet.create({})