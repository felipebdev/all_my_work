import { StyleSheet, Text, View, Image, TextInput, TouchableOpacity, ActivityIndicator, StatusBar } from 'react-native'
import React, { useState } from 'react'
import { MaterialIcons } from '@expo/vector-icons';

const Registraremail = ({ navigation }) => {
    const [email, setEmail] = useState('')
    const [loading, setLoading] = useState(false)
    const handleEmail = () => {
        // setLoading(true)
        // navigation.navigate('Signup_EnterVerificationCode')
        if (email == '') {
            alert('Please enter email')
        }
        else {
            setLoading(true)
            fetch('http://192.168.0.54:3000/verificaremail', {
                method: 'post',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    email: email
                })
            })
                .then(res => res.json()).then(
                    data => {
                        if (data.error === "Credenciais inválidas") {
                            // alert('Invalid Credentials')
                            alert('Credenciais inválidas')
                            setLoading(false)
                        }
                        else if (data.message === "Código de verificação enviado para o seu email") {
                            setLoading(false)
                            navigation.navigate('Registrarverificaremail', {
                                useremail: data.email,
                                userVerificationCode: data.VerificationCode
                            })

                        }
                    }
                )
        }
    }
    return (
        <View style={{ width: '100%', height: '100%'}}>
             <StatusBar hidden={true}/>
            <TouchableOpacity onPress={() => navigation.navigate('Login')} >

               <Image style={{alignSelf: 'flex-end', marginTop: 20, marginEnd: 20, width: 20, height: 20}} source={require('../../images/saircards.png')}/>

            </TouchableOpacity>

            <View style={{width: '100%', justifyContent: 'center', alignItems: 'center', marginTop: 50}}>
            
            <Text  style={{fontSize: 25, fontWeight: 'bold'}}>Criar uma nova conta</Text>
            
            <Text style={{ textAlign: 'left', fontSize: 14, marginTop: 10,}}>Escolha um email para sua nova conta.{'\n'}Receberá um código em seu email.</Text>
            
            <TextInput style={{width: '95%', height: 40, borderColor: 'gray', borderWidth: 1, borderRadius: 5, marginTop: 20, backgroundColor: 'white',
            padding: 10}} placeholder="example@example.com" 

                onChangeText={(text) => {
                    setEmail(text)
                }}
            />
            {
                loading ?
                    <ActivityIndicator size="large" color="white" style={{marginTop: 20}} />
                    :
                    <TouchableOpacity  style={{width: '95%', height: 40, borderRadius: 5, marginTop: 20, backgroundColor: '#ec230d',
                    color: 'white', fontSize: 18, alignItems: 'center', justifyContent: 'center'}} onPress={() => handleEmail()}>
                    <Text style={{fontSize: 15, color: 'white', fontWeight: 'bold'}}
                    
                    >
                        Avançar
                    </Text>
                    </TouchableOpacity>
            }
            </View>
        </View>
    )
}

export default Registraremail

const styles = StyleSheet.create({})